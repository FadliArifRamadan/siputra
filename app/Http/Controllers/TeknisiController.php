<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBuktiPerbaikanRequest;
use App\Models\BuktiPerbaikan;
use App\Models\PenugasanTeknisi;
use Illuminate\Http\RedirectResponse;
use App\Models\Fasilitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Services\SlaService;
use App\Services\NotificationService;
use App\Http\Requests\StorePinjamanAlatRequest;
use App\Http\Requests\UpdateFasilitasStatusRequest;
use App\Http\Requests\UpdateProgressRequest;
use App\Http\Requests\VerifikasiSerahTerimaRequest;
use App\Models\PeminjamanFasilitas;
use App\Models\PengajuanKerusakan;
use App\Models\User;
use App\Services\BookingValidationService;


class TeknisiController extends Controller
{
    protected NotificationService $notificationService;
    protected SlaService $slaService;
    protected BookingValidationService $bookingService;

    public function __construct(NotificationService $notificationService, SlaService $slaService, BookingValidationService $bookingService)
    {
        $this->notificationService = $notificationService;
        $this->slaService = $slaService;
        $this->bookingService = $bookingService;
    }

    public function index(): View
    {
        $userId = Auth::id();

        $tugasAktif = PenugasanTeknisi::with(['pengajuanKerusakan.pelapor'])
            ->where('teknisi_id', $userId)
            ->whereHas('pengajuanKerusakan', function ($query) {
                $query->where('status_laporan', 'diproses');
            })
            ->orderBy('jadwal_penanganan', 'asc')
            ->get();

        $tugasSelesai = PenugasanTeknisi::with(['pengajuanKerusakan.pelapor'])
            ->where('teknisi_id', $userId)
            ->whereHas('pengajuanKerusakan', function ($query) {
                $query->where('status_laporan', 'selesai');
            })
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        $tugasMenunggu = PenugasanTeknisi::with(['pengajuanKerusakan.pelapor'])
            ->where('teknisi_id', $userId)
            ->whereHas('pengajuanKerusakan', function ($query) {
                $query->where('status_laporan', 'pending');
            })
            ->orderBy('jadwal_penanganan', 'asc')
            ->get();

        $statistik = [
            'total' => PenugasanTeknisi::where('teknisi_id', $userId)->count(),
            'aktif' => $tugasAktif->count(),
            'selesai' => $tugasSelesai->count(),
            'menunggu' => $tugasMenunggu->count(),
        ];

        return view('teknisi.dashboard', compact(
            'tugasAktif',
            'tugasSelesai',
            'tugasMenunggu',
            'statistik'
        ));
    }

    public function show(PenugasanTeknisi $penugasan): View|RedirectResponse
    {
        if ($penugasan->teknisi_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke tugas ini.');
        }

        $penugasan->load(['pengajuanKerusakan.pelapor', 'teknisi', 'buktiPerbaikan']);

        // ✅ CEK: Jika laporan sudah selesai (karena fasilitas sudah baik), redirect ke halaman laporan
        if ($penugasan->pengajuanKerusakan->status_laporan === 'selesai') {
            return redirect()->route('teknisi.laporan-kerusakan.show', $penugasan->pengajuan_id)
                ->with('info', 'Laporan ini sudah selesai karena status fasilitas sudah "baik".');
        }

        // ✅ CEK: Jika fasilitas sudah baik tapi laporan belum selesai (fallback)
        $fasilitas = $penugasan->pengajuanKerusakan->fasilitas;
        if ($fasilitas && $fasilitas->status_kondisi === 'baik') {
            // Tutup laporan otomatis
            $this->closePendingLaporanByFasilitas($fasilitas, 'Otomatis ditutup saat akses halaman tugas');
            return redirect()->route('teknisi.laporan-kerusakan.show', $penugasan->pengajuan_id)
                ->with('success', 'Laporan otomatis selesai karena fasilitas sudah "baik".');
        }

        $progressOptions = $this->getAvailableProgress($penugasan->pengajuanKerusakan->status_laporan);

        return view('teknisi.tugas.show', compact('penugasan', 'progressOptions'));
    }

    public function closeForm(PenugasanTeknisi $penugasan): View|RedirectResponse
    {
        if ($penugasan->teknisi_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke tugas ini.');
        }

        if ($penugasan->pengajuanKerusakan->status_laporan === 'selesai') {
            return redirect()->route('teknisi.tugas.show', $penugasan)
                ->with('error', 'Tugas ini sudah selesai.');
        }
        // ✅ CEK: Jika laporan sudah selesai
        if ($penugasan->pengajuanKerusakan->status_laporan === 'selesai') {
            return redirect()->route('teknisi.laporan-kerusakan.show', $penugasan->pengajuan_id)
                ->with('info', 'Laporan ini sudah selesai. Tidak perlu close ticket lagi.');
        }

        // ✅ CEK: Jika fasilitas sudah baik, tutup otomatis
        $fasilitas = $penugasan->pengajuanKerusakan->fasilitas;
        if ($fasilitas && $fasilitas->status_kondisi === 'baik') {
            $this->closePendingLaporanByFasilitas($fasilitas, 'Ditutup otomatis saat akses form close');
            return redirect()->route('teknisi.laporan-kerusakan.show', $penugasan->pengajuan_id)
                ->with('success', 'Laporan otomatis selesai karena fasilitas sudah "baik".');
        }

        // ✅ Ambil status kondisi terakhir fasilitas
        $fasilitas = null;
        if ($penugasan->pengajuanKerusakan->fasilitas_id) {
            $fasilitas = Fasilitas::find($penugasan->pengajuanKerusakan->fasilitas_id);
        }

        return view('teknisi.tugas.close', compact('penugasan', 'fasilitas'));
    }

    public function storeClose(StoreBuktiPerbaikanRequest $request, PenugasanTeknisi $penugasan): RedirectResponse
    {
        // 1. Cek Hak Akses Teknisi
        if ($penugasan->teknisi_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke tugas ini.');
        }

        // Inisialisasi variabel $pengajuan agar ringkas
        $pengajuan = $penugasan->pengajuanKerusakan;

        // ✅ CEK 1: Apakah laporan sudah selesai?
        if ($pengajuan->status_laporan === 'selesai') {
            return redirect()->route('teknisi.tugas.show', $penugasan)
                ->with('warning', 'Laporan ini sudah selesai. Tidak perlu close ticket lagi.');
        }

        // ✅ CEK 2: Apakah fasilitas sudah baik?
        if ($pengajuan->fasilitas && $pengajuan->fasilitas->status_kondisi === 'baik') {
            // Tawarkan untuk menutup laporan tanpa upload bukti
            return redirect()->route('teknisi.tugas.show', $penugasan)
                ->with('info', 'Fasilitas sudah dalam kondisi "baik". Anda dapat menutup laporan tanpa upload bukti.');
        }

        // 2. Jika semua validasi awal lolos, jalankan validasi request
        $validated = $request->validated();

        // 3. Proses upload dan resize foto
        $fotoPath = null;
        if ($request->hasFile('foto_selesai')) {
            $file = $request->file('foto_selesai');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getPathname());
            $image->scale(width: 800, height: 600);

            $path = 'foto_perbaikan/' . $filename;
            Storage::disk('public')->put($path, $image->toJpeg(80));
            $fotoPath = $path;
        }

        // 4. Eksekusi simpan data ke Database
        DB::transaction(function () use ($penugasan, $pengajuan, $validated, $fotoPath) {
            // 1. Buat bukti perbaikan
            BuktiPerbaikan::create([
                'penugasan_id' => $penugasan->id,
                'foto_selesai' => $fotoPath,
                'keterangan_selesai' => $validated['keterangan_selesai'],
                'tanggal_selesai' => now(),
            ]);

            // 2. Update status laporan menjadi 'selesai'
            $oldStatus = $pengajuan->status_laporan;
            $pengajuan->update(['status_laporan' => 'selesai']);

            // ✅ Clear SLA status
            $this->slaService->clearSlaStatus($pengajuan);

            // Log status change untuk laporan
            $pengajuan->logStatusChange('selesai', $oldStatus, 'Tugas diselesaikan oleh teknisi: ' . $validated['keterangan_selesai']);

            // ✅ 3. Update status kondisi fasilitas berdasarkan input teknisi (jika ada)
            if ($pengajuan->fasilitas_id && !empty($validated['kondisi_akhir'])) {
                $fasilitas = Fasilitas::find($pengajuan->fasilitas_id);
                if ($fasilitas) {
                    $oldKondisi = $fasilitas->status_kondisi;
                    $newKondisi = $validated['kondisi_akhir'];

                    // Konversi 'perlu_tindak_lanjut' menjadi 'maintenance' untuk konsistensi database
                    if ($newKondisi === 'perlu_tindak_lanjut') {
                        $newKondisi = 'maintenance';
                    }

                    if ($oldKondisi !== $newKondisi) {
                        $fasilitas->update(['status_kondisi' => $newKondisi]);

                        // Log perubahan status fasilitas
                        $fasilitas->logStatusChange(
                            $newKondisi,
                            $oldKondisi,
                            'Diperbaiki melalui laporan #' . $pengajuan->id . ' oleh teknisi ' . Auth::user()->nama .
                                '. Catatan: ' . $validated['keterangan_selesai']
                        );
                    }
                }
            }

            // ✅ Notifikasi + email ke pelapor
            $this->notificationService->notify(
                $pengajuan->pelapor,
                'Laporan Kerusakan Selesai',
                'Laporan Anda "' . $pengajuan->jenis_kerusakan . '" telah selesai diperbaiki.',
                'ticket_completed',
                route('pelapor.pengajuan.show', $pengajuan),
                $pengajuan,
                true // send email
            );
        });

        return redirect()->route('teknisi.tugas.show', $penugasan)
            ->with('success', 'Tugas berhasil diselesaikan! Bukti perbaikan telah tersimpan.');
    }

    // ============================================================
    // 2. UPDATE PROGRESS LAPORAN KERUSAKAN (POIN 9)
    // ============================================================

    public function updateProgress(UpdateProgressRequest $request, PenugasanTeknisi $penugasan): RedirectResponse
    {
        if ($penugasan->teknisi_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke tugas ini.');
        }

        $pengajuan = $penugasan->pengajuanKerusakan;

        // Cek apakah progress bisa diupdate (hanya maju)
        $availableProgress = $this->getAvailableProgress($pengajuan->status_laporan);
        $newProgress = $request->status_laporan;

        if (!in_array($newProgress, $availableProgress)) {
            return redirect()->back()
                ->with('error', 'Progress tidak dapat diubah ke status "' . $newProgress . '". Hanya bisa maju ke status selanjutnya.');
        }

        $oldStatus = $pengajuan->status_laporan;
        $pengajuan->update(['status_laporan' => $newProgress]);
        $pengajuan->logStatusChange($newProgress, $oldStatus, $request->catatan ?? 'Update progress oleh teknisi');

        // Notifikasi ke RTK jika status menunggu_parts atau selesai
        if (in_array($newProgress, ['menunggu_parts', 'selesai'])) {
            $rtkUsers = User::role('rtk')->get();
            $message = $newProgress === 'menunggu_parts'
                ? 'Teknisi ' . Auth::user()->nama . ' menunggu suku cadang untuk laporan "' . $pengajuan->jenis_kerusakan . '"'
                : 'Teknisi ' . Auth::user()->nama . ' telah menyelesaikan laporan "' . $pengajuan->jenis_kerusakan . '" (menunggu verifikasi)';

            foreach ($rtkUsers as $rtk) {
                $this->notificationService->create(
                    $rtk,
                    $newProgress === 'menunggu_parts' ? '⏳ Menunggu Parts' : '✅ Laporan Selesai',
                    $message,
                    $newProgress === 'menunggu_parts' ? 'waiting_parts' : 'task_completed_teknisi',
                    route('rtk.laporan-kerusakan.show', $pengajuan),
                    $pengajuan
                );
            }
        }

        return redirect()->route('teknisi.tugas.show', $penugasan)
            ->with('success', 'Progress berhasil diupdate ke "' . $newProgress . '".');
    }

    private function getAvailableProgress(string $currentStatus): array
    {
        $progressFlow = ['pending', 'diterima', 'dikerjakan', 'menunggu_parts', 'selesai'];
        $currentIndex = array_search($currentStatus, $progressFlow);

        if ($currentIndex === false || $currentIndex >= count($progressFlow) - 1) {
            return [];
        }

        // Hanya return 1 status selanjutnya (maju satu langkah)
        return [$progressFlow[$currentIndex + 1]];
    }

    // ============================================================
    // 3. UPDATE STATUS FASILITAS (POIN 9)
    // ============================================================

    public function showFasilitas(Fasilitas $fasilitas): View
    {
        $fasilitas->load(['lokasi', 'statusLogs.actor']);
        return view('teknisi.fasilitas.show', compact('fasilitas'));
    }

    public function updateStatusFasilitas(UpdateFasilitasStatusRequest $request, Fasilitas $fasilitas): RedirectResponse
    {
        $oldStatus = $fasilitas->status_kondisi;
        $newStatus = $request->status_kondisi;

        if ($oldStatus === $newStatus) {
            return redirect()->back()
                ->with('info', 'Status kondisi sudah ' . $newStatus . ', tidak ada perubahan.');
        }

        DB::transaction(function () use ($fasilitas, $oldStatus, $newStatus, $request) {
            // 1. Update status fasilitas
            $fasilitas->update(['status_kondisi' => $newStatus]);
            $fasilitas->logStatusChange(
                $newStatus,
                $oldStatus,
                $request->catatan ?? 'Update status oleh teknisi ' . Auth::user()->nama
            );

            // ✅ 2. Jika fasilitas berubah menjadi "baik", tutup semua laporan terkait
            if ($newStatus === 'baik') {
                $this->closePendingLaporanByFasilitas($fasilitas, $request->catatan);
            }
        });

        return redirect()->route('teknisi.fasilitas.show', $fasilitas)
            ->with('success', 'Status fasilitas berhasil diperbarui dari "' . $oldStatus . '" menjadi "' . $newStatus . '".');
    }

    /**
     * ✅ Tutup laporan pending yang terkait dengan fasilitas ini
     */
    private function closePendingLaporanByFasilitas(Fasilitas $fasilitas, ?string $catatan = null): void
    {
        // Cari laporan yang statusnya pending atau diproses dan terkait dengan fasilitas ini
        $laporan = PengajuanKerusakan::where('fasilitas_id', $fasilitas->id)
            ->whereIn('status_laporan', ['pending', 'diproses', 'diterima', 'dikerjakan', 'menunggu_parts'])
            ->whereNull('deleted_at')
            ->get();

        foreach ($laporan as $item) {
            $oldStatus = $item->status_laporan;

            // ✅ Update status laporan menjadi selesai
            $item->update([
                'status_laporan' => 'selesai',
                'catatan_rtk' => ($item->catatan_rtk ? $item->catatan_rtk . "\n" : '') .
                    '[SELESAI OTOMATIS] Status fasilitas "' . $fasilitas->nama_fasilitas . '" diubah menjadi "baik" oleh ' . Auth::user()->nama .
                    ($catatan ? ' - Catatan: ' . $catatan : '')
            ]);

            // Log perubahan status laporan
            $item->logStatusChange(
                'selesai',
                $oldStatus,
                'Otomatis selesai karena status fasilitas "' . $fasilitas->nama_fasilitas . '" diubah menjadi "baik" oleh ' . Auth::user()->nama
            );

            // ✅ Jika ada penugasan, clear SLA
            if ($item->penugasanTeknisi) {
                $this->slaService->clearSlaStatus($item);
            }

            // ✅ Notifikasi ke pelapor
            $this->notificationService->create(
                $item->pelapor,
                '✅ Laporan Selesai Otomatis',
                'Laporan "' . $item->jenis_kerusakan . '" selesai karena status fasilitas "' . $fasilitas->nama_fasilitas . '" diperbarui menjadi "baik".',
                'ticket_completed',
                route('pelapor.pengajuan.show', $item),
                $item
            );

            // ✅ Notifikasi ke semua RTK
            $rtkUsers = User::role('rtk')->get();
            foreach ($rtkUsers as $rtk) {
                $this->notificationService->create(
                    $rtk,
                    '✅ Laporan Selesai Otomatis',
                    'Laporan #' . $item->id . ' (' . $item->jenis_kerusakan . ') selesai otomatis karena status fasilitas "' . $fasilitas->nama_fasilitas . '" diubah menjadi "baik".',
                    'ticket_completed_auto',
                    route('rtk.laporan-kerusakan.show', $item),
                    $item
                );
            }
        }
    }

    // ============================================================
    // 4. PINJAMAN ALAT KERJA (POIN 9)
    // ============================================================

    public function indexPinjamanAlat(): View
    {
        $userId = Auth::id();

        // ✅ PERBAIKAN: Gunakan 'items.fasilitas' & 'whereHas' pada 'items.fasilitas'
        $peminjaman = PeminjamanFasilitas::with(['items.fasilitas', 'peminjam'])
            ->where('peminjam_id', $userId)
            ->whereHas('items.fasilitas', function ($query) {
                $query->where('kategori', 'sarana');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('teknisi.peminjaman-alat.index', compact('peminjaman'));
    }

    public function createPinjamanAlat(): View
    {
        // Hanya tampilkan sarana (alat) dengan status baik atau rusak_ringan
        $fasilitas = Fasilitas::where('kategori', 'sarana')
            ->whereIn('status_kondisi', ['baik', 'rusak_ringan'])
            ->orderBy('nama_fasilitas')
            ->get();

        return view('teknisi.peminjaman-alat.create', compact('fasilitas'));
    }

    public function storePinjamanAlat(StorePinjamanAlatRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // 1. Validasi Stok tiap item
        foreach ($validated['items'] as $item) {
            $validationResult = $this->bookingService->validateBooking(
                $item['fasilitas_id'],
                $item['jumlah_dipinjam'],
                $validated['waktu_mulai'],
                $validated['waktu_selesai']
            );

            if (!$validationResult['valid']) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', implode(' ', $validationResult['errors']));
            }
        }

        // 2. Simpan Header & Items
        DB::transaction(function () use ($validated) {
            // Buat Header (tanpa fasilitas_id)
            $peminjaman = PeminjamanFasilitas::create([
                'peminjam_id' => Auth::id(),
                'waktu_mulai' => $validated['waktu_mulai'],
                'waktu_selesai' => $validated['waktu_selesai'],
                'keperluan' => $validated['keperluan'],
                'status_pinjam' => 'pending',
            ]);

            // Buat Items
            foreach ($validated['items'] as $item) {
                $peminjaman->items()->create([
                    'fasilitas_id' => $item['fasilitas_id'],
                    'jumlah_dipinjam' => $item['jumlah_dipinjam'],
                    'status_item' => 'pending',
                ]);
            }

            // Notifikasi ke RTK
            $rtkUsers = User::role('rtk')->get();
            foreach ($rtkUsers as $rtk) {
                $this->notificationService->create(
                    $rtk,
                    '🔧 Pengajuan Pinjaman Alat',
                    'Teknisi ' . Auth::user()->nama . ' mengajukan pinjaman alat.',
                    'teknisi_pinjaman_alat',
                    route('rtk.peminjaman.index'),
                    null
                );
            }
        });

        return redirect()->route('teknisi.peminjaman-alat.index')
            ->with('success', 'Pengajuan pinjaman alat berhasil! Menunggu persetujuan RTK.');
    }

    public function showPinjamanAlat(PeminjamanFasilitas $peminjaman): View
    {
        if ($peminjaman->peminjam_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke peminjaman ini.');
        }

        // Load relasi items.fasilitas
        $peminjaman->load(['items.fasilitas', 'peminjam', 'verifikasiSerahTerimaBy', 'verifikasiPengembalianBy']);

        // ✅ PERBAIKAN: Cek kategori sarana dari items
        $hasSarana = $peminjaman->items->contains(function ($item) {
            return $item->fasilitas && $item->fasilitas->kategori === 'sarana';
        });

        if (!$hasSarana) {
            abort(404, 'Peminjaman ini bukan untuk alat kerja.');
        }

        return view('teknisi.peminjaman-alat.show', compact('peminjaman'));
    }

    // ============================================================
    // 5. VERIFIKASI SERAH TERIMA & PENGEMBALIAN (POIN 9)
    // ============================================================

    public function verifikasiSerahTerima(VerifikasiSerahTerimaRequest $request, PeminjamanFasilitas $peminjaman): RedirectResponse
    {
        $peminjaman->load('items.fasilitas');

        // Cek: status harus disetujui
        if ($peminjaman->status_pinjam !== 'disetujui') {
            return redirect()->back()
                ->with('error', 'Peminjaman harus berstatus disetujui untuk verifikasi serah terima.');
        }

        // Cek: sudah diverifikasi sebelumnya
        if ($peminjaman->isSudahDiverifikasiSerahTerima()) {
            return redirect()->back()
                ->with('error', 'Serah terima sudah diverifikasi sebelumnya.');
        }

        $peminjaman->update([
            'verifikasi_serah_terima_by' => Auth::id(),
            'verifikasi_serah_terima_at' => now(),
            'kondisi_serah_terima' => $request->kondisi,
            'catatan_serah_terima' => $request->catatan,
            'status_pinjam' => 'berjalan',
        ]);

        // Log status change
        $peminjaman->logStatusChange('berjalan', 'disetujui', 'Verifikasi serah terima oleh teknisi: ' . Auth::user()->nama);

        // ✅ Ambil nama barang/alat dari item pertama
        $namaBarang = $peminjaman->items->first()->fasilitas->nama_fasilitas ?? 'Alat Kerja';

        // Notifikasi ke peminjam
        $this->notificationService->create(
            $peminjaman->peminjam,
            '📦 Barang Diserahkan',
            'Barang "' . $namaBarang . '" telah diserahkan dan dalam kondisi ' . $request->kondisi . '.',
            'barang_diserahkan',
            route('pelapor.peminjaman.show', $peminjaman),
            $peminjaman
        );

        return redirect()->route('teknisi.peminjaman-alat.show', $peminjaman)
            ->with('success', 'Verifikasi serah terima berhasil. Status peminjaman: Berjalan.');
    }

    public function verifikasiPengembalian(VerifikasiSerahTerimaRequest $request, PeminjamanFasilitas $peminjaman): RedirectResponse
    {
        $peminjaman->load('items.fasilitas');

        // Cek: status harus berjalan
        if ($peminjaman->status_pinjam !== 'berjalan') {
            return redirect()->back()
                ->with('error', 'Peminjaman harus berstatus berjalan untuk verifikasi pengembalian.');
        }

        // Cek: belum diverifikasi serah terima
        if (!$peminjaman->isSudahDiverifikasiSerahTerima()) {
            return redirect()->back()
                ->with('error', 'Serah terima harus diverifikasi terlebih dahulu.');
        }

        // Cek: sudah diverifikasi pengembalian sebelumnya
        if ($peminjaman->isSudahDiverifikasiPengembalian()) {
            return redirect()->back()
                ->with('error', 'Pengembalian sudah diverifikasi sebelumnya.');
        }

        $peminjaman->update([
            'verifikasi_pengembalian_by' => Auth::id(),
            'verifikasi_pengembalian_at' => now(),
            'kondisi_pengembalian' => $request->kondisi,
            'catatan_pengembalian' => $request->catatan,
            'status_pinjam' => 'selesai',
        ]);

        // Log status change
        $peminjaman->logStatusChange('selesai', 'berjalan', 'Verifikasi pengembalian oleh teknisi: ' . Auth::user()->nama);

        // ✅ Ambil nama barang/alat dari item pertama
        $namaBarang = $peminjaman->items->first()->fasilitas->nama_fasilitas ?? 'Alat Kerja';

        // Notifikasi ke peminjam
        $this->notificationService->create(
            $peminjaman->peminjam,
            '📦 Barang Dikembalikan',
            'Barang "' . $namaBarang . '" telah dikembalikan dan dalam kondisi ' . $request->kondisi . '.',
            'barang_dikembalikan',
            route('pelapor.peminjaman.show', $peminjaman),
            $peminjaman
        );

        return redirect()->route('teknisi.peminjaman-alat.show', $peminjaman)
            ->with('success', 'Verifikasi pengembalian berhasil. Status peminjaman: Selesai.');
    }

    // ============================================================
    // 6. LAPORAN KERUSAKAN (TEKNISI — hanya miliknya)
    // ============================================================

    public function indexLaporan(): View
    {
        $userId = Auth::id();

        // Ambil semua laporan yang ditugaskan ke teknisi ini
        $laporan = PengajuanKerusakan::with(['pelapor', 'fasilitas', 'penugasanTeknisi.teknisi'])
            ->whereHas('penugasanTeknisi', function ($query) use ($userId) {
                $query->where('teknisi_id', $userId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('teknisi.laporan-kerusakan.index', compact('laporan'));
    }

    public function showLaporan(PengajuanKerusakan $pengajuan): View
    {
        // Pastikan laporan ini ditugaskan ke teknisi yang login
        if (!$pengajuan->penugasanTeknisi || $pengajuan->penugasanTeknisi->teknisi_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke laporan ini.');
        }

        $pengajuan->load(['pelapor', 'fasilitas', 'penugasanTeknisi.teknisi', 'penugasanTeknisi.buktiPerbaikan', 'statusLogs.actor']);

        return view('teknisi.laporan-kerusakan.show', compact('pengajuan'));
    }

    // ============================================================
    // 7. FASILITAS (TEKNISI — lihat semua fasilitas)
    // ============================================================

    public function indexFasilitas(): View
    {
        $fasilitas = Fasilitas::orderBy('nama_fasilitas')->paginate(15);
        return view('teknisi.fasilitas.index', compact('fasilitas'));
    }
}
