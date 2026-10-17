<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\FasilitasDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFasilitasRequest;
use App\Http\Requests\UpdateFasilitasRequest;
use App\Models\Fasilitas;
use App\Services\QrCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\PengajuanKerusakan;
use App\Models\User;
use App\Services\SlaService;
use App\Services\NotificationService;


class FasilitasController extends Controller
{
    protected QrCodeService $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    public function index(FasilitasDataTable $dataTable)
    {
        return $dataTable->render('rtk.fasilitas.index');
    }

    public function create(): View
    {
        // ✅ Ambil data lokasi untuk dropdown
        $lokasi = Lokasi::where('is_active', true)
            ->orderBy('kampus')
            ->orderBy('lantai')
            ->orderBy('kode_ruangan')
            ->get()
            ->groupBy('kampus');

        return view('rtk.fasilitas.create', compact('lokasi'));
    }

    public function store(StoreFasilitasRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // ✅ Jika lokasi_id dipilih, ambil lokasi_detail dari data lokasi
        if (!empty($data['lokasi_id'])) {
            $lokasi = Lokasi::find($data['lokasi_id']);
            if ($lokasi) {
                $data['lokasi_detail'] = $lokasi->full_name;
            }
        }

        // ✅ Fasilitas di controller ini adalah Fasilitas Pinjam
        $data['tipe_fasilitas'] = 'pinjam';

        // ✅ Buat fasilitas dengan data yang sudah lengkap
        $fasilitas = Fasilitas::create($data);

        // ✅ Generate QR Code
        if ($request->input('generate_qr', true)) {
            $this->qrCodeService->generateAndSave($fasilitas);
        }

        return redirect()->route('rtk.fasilitas.index')
            ->with('success', 'Fasilitas berhasil ditambahkan! QR Code telah digenerate.');
    }
    public function show(Fasilitas $fasilitas): View
    {
        return view('rtk.fasilitas.show', compact('fasilitas'));
    }

    public function edit(Fasilitas $fasilitas): View
    {
        // ✅ Ambil data lokasi untuk dropdown
        $lokasi = Lokasi::where('is_active', true)
            ->orderBy('kampus')
            ->orderBy('lantai')
            ->orderBy('kode_ruangan')
            ->get()
            ->groupBy('kampus');

        return view('rtk.fasilitas.edit', compact('fasilitas', 'lokasi'));
    }

    public function update(UpdateFasilitasRequest $request, Fasilitas $fasilitas): RedirectResponse
    {
        $data = $request->validated();

        if (!empty($data['lokasi_id'])) {
            $lokasi = Lokasi::find($data['lokasi_id']);
            if ($lokasi) {
                $data['lokasi_detail'] = $lokasi->full_name;
            }
        }

        $oldStatus = $fasilitas->status_kondisi;
        $newStatus = $data['status_kondisi'] ?? $oldStatus;

        DB::transaction(function () use ($fasilitas, $data, $oldStatus, $newStatus) {
            $fasilitas->update($data);

            // ✅ Jika fasilitas berubah menjadi "baik", tutup semua laporan terkait
            if ($newStatus === 'baik' && $oldStatus !== 'baik') {
                $this->closePendingLaporanByFasilitas($fasilitas);
            }
        });

        return redirect()->route('rtk.fasilitas.index')
            ->with('success', 'Fasilitas berhasil diperbarui!');
    }


    /**
     * ✅ Tutup laporan pending yang terkait dengan fasilitas ini (untuk RTK)
     */
    private function closePendingLaporanByFasilitas(Fasilitas $fasilitas): void
    {
        $laporan = PengajuanKerusakan::where('fasilitas_id', $fasilitas->id)
            ->whereIn('status_laporan', ['pending', 'diproses', 'diterima', 'dikerjakan', 'menunggu_parts'])
            ->whereNull('deleted_at')
            ->get();

        foreach ($laporan as $item) {
            $oldStatus = $item->status_laporan;
            $item->update([
                'status_laporan' => 'selesai',
                'catatan_rtk' => ($item->catatan_rtk ? $item->catatan_rtk . "\n" : '') .
                    '[SELESAI OTOMATIS] Status fasilitas "' . $fasilitas->nama_fasilitas . '" diubah menjadi "baik" oleh RTK ' . Auth::user()->nama
            ]);

            $item->logStatusChange(
                'selesai',
                $oldStatus,
                'Otomatis selesai karena status fasilitas "' . $fasilitas->nama_fasilitas . '" diubah menjadi "baik" oleh RTK'
            );

            if ($item->penugasanTeknisi) {
                app(SlaService::class)->clearSlaStatus($item);
            }

            // Notifikasi ke pelapor
            app(NotificationService::class)->create(
                $item->pelapor,
                '✅ Laporan Selesai',
                'Laporan "' . $item->jenis_kerusakan . '" selesai karena status fasilitas "' . $fasilitas->nama_fasilitas . '" diperbarui menjadi "baik".',
                'ticket_completed',
                route('pelapor.pengajuan.show', $item),
                $item
            );
        }
    }

    public function destroy(Fasilitas $fasilitas): RedirectResponse
    {
        // Cek apakah fasilitas sedang dipinjam
        $activePeminjaman = $fasilitas->peminjamanFasilitas()
            ->whereIn('status_pinjam', ['pending', 'disetujui', 'berjalan'])
            ->exists();

        if ($activePeminjaman) {
            return redirect()->back()
                ->with('error', 'Fasilitas tidak dapat dihapus karena sedang dalam proses peminjaman.');
        }

        $fasilitas->delete();

        return redirect()->route('rtk.fasilitas.index')
            ->with('success', 'Fasilitas berhasil dihapus!');
    }
    public function history(Fasilitas $fasilitas): View
    {
        $fasilitas->load('statusLogs.actor');
        return view('rtk.fasilitas.history', compact('fasilitas'));
    }

    // ✅ TAMBAHKAN METHOD INI
    public function generateQrCode(Fasilitas $fasilitas): RedirectResponse
    {
        try {
            $path = $this->qrCodeService->generateAndSave($fasilitas);

            return redirect()->route('rtk.fasilitas.show', $fasilitas)
                ->with('success', 'QR Code berhasil dibuat!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal membuat QR Code: ' . $e->getMessage());
        }
    }

    public function regenerateQrCode(Fasilitas $fasilitas): RedirectResponse
    {
        try {
            $path = $this->qrCodeService->regenerate($fasilitas);

            return redirect()->route('rtk.fasilitas.show', $fasilitas)
                ->with('success', 'QR Code berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui QR Code: ' . $e->getMessage());
        }
    }

    public function downloadQrCode(Fasilitas $fasilitas)
    {
        $path = 'qrcodes/fasilitas_' . $fasilitas->id . '.svg';

        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            // Generate jika belum ada
            $this->qrCodeService->generateAndSave($fasilitas);
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->download($path, 'QR_' . $fasilitas->nama_fasilitas . '.svg');
    }
}
