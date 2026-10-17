<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\PenugasanDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePenugasanRequest;
use App\Http\Requests\UpdatePenugasanRequest;
use App\Models\PengajuanKerusakan;
use App\Models\PenugasanTeknisi;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use App\Services\SlaService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\BookingValidationService;
use Illuminate\View\View;
use App\Services\NotificationService;

class PenugasanController extends Controller
{
    private NotificationService $notificationService;
    protected SlaService $slaService;
    protected BookingValidationService $bookingService;

    public function __construct(NotificationService $notificationService, SlaService $slaService, BookingValidationService $bookingService)
    {
        $this->slaService = $slaService;
        $this->notificationService = $notificationService;
        $this->bookingService = $bookingService;
    }

    public function index(PenugasanDataTable $dataTable)
    {
        return $dataTable->render('rtk.penugasan.index');
    }

    public function create(Request $request): View|RedirectResponse
    {
        $pengajuanId = $request->query('pengajuan_id');

        if ($pengajuanId) {
            $pengajuan = PengajuanKerusakan::with('pelapor')->findOrFail($pengajuanId);

            // ✅ CEK: Apakah laporan sudah selesai?
            if ($pengajuan->status_laporan === 'selesai') {
                return redirect()->route('rtk.laporan-kerusakan.index')
                    ->with('error', 'Laporan ini sudah selesai. Tidak perlu menugaskan teknisi.');
            }

            // ❌ BLOK INI DINONAKTIFKAN KARENA MENYEBABKAN LOOPING
            // Jika status fasilitas "baik", biarkan RTK tetap bisa menugaskan teknisi atau menutupnya secara manual, 
            // daripada melempar kembali ke halaman show tanpa mengubah status laporannya.
            /*
            if ($pengajuan->fasilitas && $pengajuan->fasilitas->status_kondisi === 'baik') {
                return redirect()->route('rtk.laporan-kerusakan.show', $pengajuan)
                    ->with('warning', 'Fasilitas sudah dalam kondisi "baik". Laporan akan otomatis selesai.');
            }
            */

            // Pastikan pengajuan belum memiliki penugasan
            if ($pengajuan->penugasanTeknisi) {
                return redirect()->route('rtk.penugasan.index')
                    ->with('error', 'Laporan ini sudah memiliki penugasan teknisi.');
            }

            // Pastikan status laporan pending atau diproses
            if (!in_array($pengajuan->status_laporan, ['pending', 'diproses'])) {
                return redirect()->route('rtk.penugasan.index')
                    ->with('error', 'Laporan dengan status ' . $pengajuan->status_laporan . ' tidak dapat ditugaskan.');
            }
        } else {
            $pengajuan = null;
        }

        // Ambil daftar teknisi (user dengan role teknisi dan status active)
        $teknisi = User::role('teknisi')
            ->where('status_akun', 'active')
            ->orderBy('nama')
            ->get();

        // Ambil daftar pengajuan yang belum memiliki penugasan
        $pengajuanList = PengajuanKerusakan::with('pelapor')
            ->whereDoesntHave('penugasanTeknisi')
            ->whereIn('status_laporan', ['pending', 'diproses'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('rtk.penugasan.create', compact('pengajuan', 'teknisi', 'pengajuanList'));
    }

    public function store(StorePenugasanRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // ✅ Validasi bentrok jadwal teknisi
        $validation = $this->bookingService->validateTeknisiAssignment(
            $validated['teknisi_id'],
            $validated['jadwal_penanganan'],
            Carbon::parse($validated['jadwal_penanganan'])->addHour()->toDateTimeString()
        );

        if (!$validation['valid']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $validation['message']);
        }

        // Cek apakah pengajuan sudah punya penugasan
        $existing = PenugasanTeknisi::where('pengajuan_id', $request->pengajuan_id)->exists();
        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Laporan ini sudah memiliki penugasan teknisi.');
        }

        // Buat penugasan
        $penugasan = PenugasanTeknisi::create([
            'pengajuan_id' => $request->pengajuan_id,
            'teknisi_id' => $validated['teknisi_id'],
            'jadwal_penanganan' => $validated['jadwal_penanganan'],
            'instruksi_kerja' => $validated['instruksi_kerja'] ?? null,
        ]);

        // Update status laporan menjadi 'diproses'
        $pengajuan = $penugasan->pengajuanKerusakan;
        $oldStatus = $pengajuan->status_laporan;
        $penugasan->pengajuanKerusakan->update(['status_laporan' => 'diproses']);

        // ✅ Clear SLA status (sudah diproses)
        $this->slaService->clearSlaStatus($pengajuan);
        // ✅ Log status change
        $pengajuan->logStatusChange('diproses', $oldStatus, 'Ditugaskan ke teknisi: ' . $penugasan->teknisi->nama);
        // ✅ Notifikasi + email ke teknisi
        $this->notificationService->notify(
            $penugasan->teknisi,
            'Tugas Baru Diterima',
            'Anda ditugaskan untuk menangani laporan: ' . $pengajuan->jenis_kerusakan,
            'task_assigned',
            route('teknisi.tugas.show', $penugasan),
            $penugasan,
            true // send email
        );

        // ✅ Notifikasi + email ke pelapor
        $this->notificationService->notify(
            $pengajuan->pelapor,
            'Laporan Kerusakan Diproses',
            'Laporan Anda "' . $pengajuan->jenis_kerusakan . '" telah ditugaskan ke teknisi.',
            'ticket_processing',
            route('pelapor.pengajuan.show', $pengajuan),
            $pengajuan,
            false // no email untuk ini (cukup in-app)
        );


        return redirect()->route('rtk.penugasan.index')
            ->with('success', 'Teknisi berhasil ditugaskan untuk laporan ini.');
    }

    public function edit(PenugasanTeknisi $penugasan): View|RedirectResponse
    {
        // Cek apakah penugasan sudah selesai
        if ($penugasan->pengajuanKerusakan->status_laporan === 'selesai') {
            return redirect()->route('rtk.penugasan.index')
                ->with('error', 'Penugasan yang sudah selesai tidak dapat diedit.');
        }

        $teknisi = User::role('teknisi')
            ->where('status_akun', 'active')
            ->orderBy('nama')
            ->get();

        return view('rtk.penugasan.edit', compact('penugasan', 'teknisi'));
    }

    public function update(UpdatePenugasanRequest $request, PenugasanTeknisi $penugasan): RedirectResponse
    {
        if ($penugasan->pengajuanKerusakan->status_laporan === 'selesai') {
            return redirect()->route('rtk.penugasan.index')
                ->with('error', 'Penugasan yang sudah selesai tidak dapat diedit.');
        }

        $validated = $request->validated();
        // ✅ Validasi bentrok jadwal teknisi (exclude current penugasan)
        $validation = $this->bookingService->validateTeknisiAssignment(
            $validated['teknisi_id'],
            $validated['jadwal_penanganan'],
            Carbon::parse($validated['jadwal_penanganan'])->addHour()->toDateTimeString(),
            $penugasan->id
        );

        if (!$validation['valid']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $validation['message']);
        }


        $penugasan->update([
            'teknisi_id' => $validated['teknisi_id'],
            'jadwal_penanganan' => $validated['jadwal_penanganan'],
            'instruksi_kerja' => $validated['instruksi_kerja'] ?? null,
        ]);

        return redirect()->route('rtk.penugasan.index')
            ->with('success', 'Penugasan teknisi berhasil diperbarui.');
    }

    public function destroy(PenugasanTeknisi $penugasan): RedirectResponse
    {
        if ($penugasan->pengajuanKerusakan->status_laporan === 'selesai') {
            return redirect()->back()
                ->with('error', 'Penugasan yang sudah selesai tidak dapat dibatalkan.');
        }

        // Kembalikan status laporan ke 'pending'
        $pengajuan = $penugasan->pengajuanKerusakan;
        $oldStatus = $pengajuan->status_laporan;
        $pengajuan->update(['status_laporan' => 'pending']);

        // ✅ Reset SLA status ke on_track (karena kembali pending)
        $this->slaService->clearSlaStatus($pengajuan);

        // Log status change
        $pengajuan->logStatusChange('pending', $oldStatus, 'Penugasan dibatalkan oleh RTK');

        // ✅ CATATAN: Status fasilitas TIDAK diubah karena belum ada perbaikan
        // Jika fasilitas statusnya rusak_berat dan laporan dibatalkan, tetap rusak_berat

        $penugasan->delete();

        return redirect()->route('rtk.penugasan.index')
            ->with('success', 'Penugasan teknisi berhasil dibatalkan.');
    }
}