<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePengajuanKerusakanRequest;
use App\Http\Requests\UpdatePengajuanKerusakanRequest;
use App\Models\Fasilitas;
use App\Models\Lokasi;
use App\Models\User;
use App\Models\PengajuanKerusakan;
use App\Services\BookingValidationService;
use App\Services\SlaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Services\NotificationService;

class PengajuanKerusakanController extends Controller
{
    protected BookingValidationService $bookingService;
    protected SlaService $slaService;
    protected NotificationService $notificationService;

    public function __construct(BookingValidationService $bookingService, SlaService $slaService, NotificationService $notificationService)
    {
        $this->bookingService = $bookingService;
        $this->slaService = $slaService;
        $this->notificationService = $notificationService;
    }

    public function index(): View
    {
        $pengajuan = PengajuanKerusakan::with(['pelapor', 'fasilitas', 'penugasanTeknisi.teknisi'])
            ->where('pelapor_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pelapor.pengajuan.index', compact('pengajuan'));
    }

    public function create(): View
    {
        $fasilitas = Fasilitas::orderBy('nama_fasilitas')->get();

        // ✅ AMBIL DATA LOKASI UNTUK DROPDOWN
        $lokasi = Lokasi::where('is_active', true)
            ->orderBy('kampus')
            ->orderBy('lantai')
            ->orderBy('kode_ruangan')
            ->get()
            ->groupBy('kampus');

        return view('pelapor.pengajuan.create', compact('fasilitas', 'lokasi'));  // ✅ PASTIKAN 'lokasi' DIKIRIM
    }

    public function store(StorePengajuanKerusakanRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Proses upload dan resize foto
        if ($request->hasFile('foto_kerusakan')) {
            $file = $request->file('foto_kerusakan');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getPathname());
            $image->scale(width: 800, height: 600);

            $path = 'foto_kerusakan/' . $filename;
            Storage::disk('public')->put($path, $image->toJpeg(80));

            $validated['foto_kerusakan'] = $path;
        }

        $validated['pelapor_id'] = Auth::id();
        $validated['status_laporan'] = 'pending';

        // Hitung target selesai berdasarkan SLA
        $targetSelesai = $this->slaService->calculateTargetSelesai(
            now(),
            $validated['tingkat_urgensi']
        );
        $validated['target_selesai'] = $targetSelesai;
        $validated['sla_status'] = 'on_track';

        DB::transaction(function () use ($validated) {
            $pengajuan = PengajuanKerusakan::create($validated);

            // Update status kondisi fasilitas jika ada fasilitas_id
            $fasilitasUpdated = false;
            $fasilitasNama = null;
            if (!empty($validated['fasilitas_id'])) {
                $result = $this->bookingService->updateFasilitasStatusFromLaporan(
                    $validated['fasilitas_id'],
                    $validated['tingkat_urgensi'],
                    'Laporan kerusakan #' . $pengajuan->id . ' oleh ' . Auth::user()->nama
                );
                $fasilitasUpdated = $result['success'] ?? false;
                if ($fasilitasUpdated && isset($result['old_status'], $result['new_status'])) {
                    $fasilitasNama = Fasilitas::find($validated['fasilitas_id'])->nama_fasilitas ?? 'Fasilitas';
                }
            }

            // ✅ KIRIM NOTIFIKASI KE SEMUA RTK
            $rtkUsers = User::role('rtk')->get();
            $pelaporNama = Auth::user()->nama;
            $jenisKerusakan = $validated['jenis_kerusakan'];
            $lokasi = $validated['lokasi_detail'] ?? 'Tidak diketahui';
            $urgensi = ucfirst($validated['tingkat_urgensi']);

            $message = "Laporan baru dari $pelaporNama: $jenisKerusakan di $lokasi (Urgensi: $urgensi)";
            if ($fasilitasUpdated && $fasilitasNama) {
                $message .= " | Status fasilitas $fasilitasNama diperbarui.";
            }

            foreach ($rtkUsers as $rtk) {
                $this->notificationService->create(
                    $rtk,
                    '🛠️ Laporan Kerusakan Baru',
                    $message,
                    'new_report',
                    route('rtk.laporan-kerusakan.index'),
                    $pengajuan
                );
            }
        });

        return redirect()->route('pelapor.pengajuan.index')
            ->with('success', 'Laporan kerusakan berhasil diajukan! Target penyelesaian: ' . $targetSelesai->format('d/m/Y H:i'));
    }

    public function show(PengajuanKerusakan $pengajuan): View
    {
        if ($pengajuan->pelapor_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke laporan ini.');
        }

        $pengajuan->load(['pelapor', 'fasilitas', 'penugasanTeknisi.teknisi']);

        return view('pelapor.pengajuan.show', compact('pengajuan'));
    }

    public function edit(PengajuanKerusakan $pengajuan): View|RedirectResponse
    {
        if ($pengajuan->pelapor_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke laporan ini.');
        }

        if (in_array($pengajuan->status_laporan, ['diproses', 'selesai'])) {
            return redirect()->route('pelapor.pengajuan.index')
                ->with('error', 'Laporan yang sudah diproses tidak dapat diedit.');
        }

        $fasilitas = Fasilitas::orderBy('nama_fasilitas')->get();

        // ✅ AMBIL DATA LOKASI UNTUK DROPDOWN DI EDIT
        $lokasi = Lokasi::where('is_active', true)
            ->orderBy('kampus')
            ->orderBy('lantai')
            ->orderBy('kode_ruangan')
            ->get()
            ->groupBy('kampus');

        return view('pelapor.pengajuan.edit', compact('pengajuan', 'fasilitas', 'lokasi'));  // ✅ PASTIKAN 'lokasi' DIKIRIM
    }

    public function update(UpdatePengajuanKerusakanRequest $request, PengajuanKerusakan $pengajuan): RedirectResponse
    {
        if ($pengajuan->pelapor_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke laporan ini.');
        }

        if (in_array($pengajuan->status_laporan, ['diproses', 'selesai'])) {
            return redirect()->route('pelapor.pengajuan.index')
                ->with('error', 'Laporan yang sudah diproses tidak dapat diedit.');
        }

        $validated = $request->validated();

        // Proses upload dan resize foto baru jika ada
        if ($request->hasFile('foto_kerusakan')) {
            if ($pengajuan->foto_kerusakan && Storage::disk('public')->exists($pengajuan->foto_kerusakan)) {
                Storage::disk('public')->delete($pengajuan->foto_kerusakan);
            }

            $file = $request->file('foto_kerusakan');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getPathname());
            $image->scale(width: 800, height: 600);

            $path = 'foto_kerusakan/' . $filename;
            Storage::disk('public')->put($path, $image->toJpeg(80));

            $validated['foto_kerusakan'] = $path;
        }

        // Jika fasilitas_id dipilih dan lokasi_detail kosong, isi dari data fasilitas
        if (!empty($validated['fasilitas_id']) && empty($validated['lokasi_detail'])) {
            $fasilitas = Fasilitas::find($validated['fasilitas_id']);
            if ($fasilitas) {
                $validated['lokasi_detail'] = $fasilitas->lokasi_detail;
            }
        }

        $pengajuan->update($validated);

        return redirect()->route('pelapor.pengajuan.index')
            ->with('success', 'Laporan kerusakan berhasil diperbarui!');
    }

    public function destroy(PengajuanKerusakan $pengajuan): RedirectResponse
    {
        if ($pengajuan->pelapor_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke laporan ini.');
        }

        if (in_array($pengajuan->status_laporan, ['diproses', 'selesai'])) {
            return redirect()->route('pelapor.pengajuan.index')
                ->with('error', 'Laporan yang sudah diproses tidak dapat dihapus.');
        }

        if ($pengajuan->foto_kerusakan && Storage::disk('public')->exists($pengajuan->foto_kerusakan)) {
            Storage::disk('public')->delete($pengajuan->foto_kerusakan);
        }

        $pengajuan->delete();

        return redirect()->route('pelapor.pengajuan.index')
            ->with('success', 'Laporan kerusakan berhasil dihapus!');
    }
}
