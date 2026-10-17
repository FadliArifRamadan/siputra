<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePeminjamanRequest;
use App\Http\Requests\UpdatePeminjamanRequest;
use App\Models\Fasilitas;
use App\Models\PeminjamanFasilitas;
use App\Models\PeminjamanFasilitasItem;
use App\Models\User;
use App\Services\BookingValidationService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PeminjamanFasilitasController extends Controller
{
    protected BookingValidationService $bookingService;
    protected NotificationService $notificationService;

    public function __construct(BookingValidationService $bookingService, NotificationService $notificationService)
    {
        $this->bookingService = $bookingService;
        $this->notificationService = $notificationService;
    }

    public function index(): View
    {
        $peminjaman = PeminjamanFasilitas::with(['peminjam', 'items.fasilitas'])
            ->where('peminjam_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pelapor.peminjaman.index', compact('peminjaman'));
    }

    public function create(): View
    {
        $fasilitas = Fasilitas::pinjam()
            ->whereIn('status_kondisi', ['baik', 'rusak_ringan'])
            ->orderBy('nama_fasilitas')
            ->get();

        return view('pelapor.peminjaman.create', compact('fasilitas'));
    }

    public function store(StorePeminjamanRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // ✅ FIX: Konsolidasi/gabungkan item ganda jika ada item dengan fasilitas_id sama
        $aggregatedItems = [];
        foreach ($validated['items'] as $item) {
            $fId = $item['fasilitas_id'];
            if (isset($aggregatedItems[$fId])) {
                $aggregatedItems[$fId]['jumlah_dipinjam'] += $item['jumlah_dipinjam'];
            } else {
                $aggregatedItems[$fId] = $item;
            }
        }

        // ✅ Validasi stok untuk setiap item unik
        $errors = [];
        $fasilitasData = [];

        foreach ($aggregatedItems as $item) {
            $fasilitas = Fasilitas::find($item['fasilitas_id']);

            if (!$fasilitas) {
                $errors[] = 'Fasilitas tidak ditemukan.';
                continue;
            }

            // Cek kondisi kelayakan
            $condCheck = $this->bookingService->isFacilityAvailableForBooking($fasilitas->id);
            if (!$condCheck['available']) {
                $errors[] = $condCheck['message'];
                continue;
            }

            // Cek stok
            $stokCheck = $this->bookingService->checkStokAvailability(
                $item['fasilitas_id'],
                $item['jumlah_dipinjam'],
                $validated['waktu_mulai'],
                $validated['waktu_selesai']
            );

            if (!$stokCheck['available']) {
                $errors[] = $stokCheck['message'];
            }

            $fasilitasData[] = [
                'fasilitas' => $fasilitas,
                'jumlah' => $item['jumlah_dipinjam'],
            ];
        }

        if (!empty($errors)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Validasi peminjaman gagal:<br>' . implode('<br>', $errors));
        }

        // Upload file pendukung jika ada
        $filePath = null;
        if ($request->hasFile('file_pendukung')) {
            $file = $request->file('file_pendukung');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('file_peminjaman', $filename, 'public');
        }

        // Simpan dalam transaksi
        DB::transaction(function () use ($validated, $filePath, $fasilitasData) {
            $peminjaman = PeminjamanFasilitas::create([
                'peminjam_id' => Auth::id(),
                'keperluan' => $validated['keperluan'],
                'catatan_peminjam' => $validated['catatan_peminjam'] ?? null,
                'waktu_mulai' => $validated['waktu_mulai'],
                'waktu_selesai' => $validated['waktu_selesai'],
                'file_pendukung' => $filePath,
                'status_pinjam' => 'menunggu_bikem',
            ]);

            foreach ($fasilitasData as $data) {
                PeminjamanFasilitasItem::create([
                    'peminjaman_id' => $peminjaman->id,
                    'fasilitas_id' => $data['fasilitas']->id,
                    'jumlah_dipinjam' => $data['jumlah'],
                    'status_item' => 'menunggu_bikem',
                ]);
            }

            // Notifikasi ke seluruh petugas BIKEM
            $bikemUsers = User::role('bikem')->get();
            $pemohonTitle = Auth::user()->hasRole('bikem') ? 'Petugas BIKEM (' . Auth::user()->nama . ')' : 'Mahasiswa ' . Auth::user()->nama;
            foreach ($bikemUsers as $bikem) {
                $this->notificationService->create(
                    $bikem,
                    '📩 Pengajuan Peminjaman Baru',
                    $pemohonTitle . ' mengajukan izin peminjaman fasilitas (' . count($fasilitasData) . ' item). Menunggu persetujuan BIKEM.',
                    'new_booking_submission',
                    route('bikem.peminjaman.show', $peminjaman),
                    $peminjaman
                );
            }
        });

        return redirect()->route('pelapor.peminjaman.index')
            ->with('success', 'Pengajuan peminjaman fasilitas berhasil dikirim! Menunggu peninjauan oleh BIKEM / Kemahasiswaan.');
    }

    public function show(PeminjamanFasilitas $peminjaman): View
    {
        if ($peminjaman->peminjam_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke peminjaman ini.');
        }

        $peminjaman->load(['peminjam', 'items.fasilitas', 'verifikasiSerahTerimaBy', 'verifikasiPengembalianBy']);

        return view('pelapor.peminjaman.show', compact('peminjaman'));
    }

    public function edit(PeminjamanFasilitas $peminjaman): View|RedirectResponse
    {
        if ($peminjaman->peminjam_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke peminjaman ini.');
        }

        if ($peminjaman->waktu_mulai <= now()) {
            return redirect()->route('pelapor.peminjaman.index')
                ->with('error', 'Peminjaman yang sudah memasuki hari pelaksanaan tidak dapat diedit.');
        }

        if (!in_array($peminjaman->status_pinjam, ['pending', 'menunggu_bikem', 'perlu_revisi'])) {
            return redirect()->route('pelapor.peminjaman.index')
                ->with('error', 'Peminjaman yang sudah diproses tidak dapat diedit.');
        }

        $fasilitas = Fasilitas::pinjam()
            ->whereIn('status_kondisi', ['baik', 'rusak_ringan'])
            ->orderBy('nama_fasilitas')
            ->get();

        $peminjaman->load(['items.fasilitas']);

        return view('pelapor.peminjaman.edit', compact('peminjaman', 'fasilitas'));
    }

    public function update(UpdatePeminjamanRequest $request, PeminjamanFasilitas $peminjaman): RedirectResponse
    {
        if ($peminjaman->peminjam_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke peminjaman ini.');
        }

        if ($peminjaman->waktu_mulai <= now()) {
            return redirect()->route('pelapor.peminjaman.index')
                ->with('error', 'Peminjaman yang sudah memasuki hari pelaksanaan tidak dapat diedit.');
        }

        if (!in_array($peminjaman->status_pinjam, ['pending', 'menunggu_bikem', 'perlu_revisi'])) {
            return redirect()->route('pelapor.peminjaman.index')
                ->with('error', 'Peminjaman yang sudah diproses tidak dapat diedit.');
        }

        $validated = $request->validated();

        // ✅ Konsolidasi item ganda
        $aggregatedItems = [];
        foreach ($validated['items'] as $item) {
            $fId = $item['fasilitas_id'];
            if (isset($aggregatedItems[$fId])) {
                $aggregatedItems[$fId]['jumlah_dipinjam'] += $item['jumlah_dipinjam'];
            } else {
                $aggregatedItems[$fId] = $item;
            }
        }

        $errors = [];
        $fasilitasData = [];

        foreach ($aggregatedItems as $item) {
            $fasilitas = Fasilitas::find($item['fasilitas_id']);

            if (!$fasilitas) {
                $errors[] = 'Fasilitas tidak ditemukan.';
                continue;
            }

            // Cek kondisi kelayakan
            $condCheck = $this->bookingService->isFacilityAvailableForBooking($fasilitas->id);
            if (!$condCheck['available']) {
                $errors[] = $condCheck['message'];
                continue;
            }

            // Cek stok (dengan excludePeminjamanId)
            $stokCheck = $this->bookingService->checkStokAvailability(
                $item['fasilitas_id'],
                $item['jumlah_dipinjam'],
                $validated['waktu_mulai'],
                $validated['waktu_selesai'],
                $peminjaman->id
            );

            if (!$stokCheck['available']) {
                $errors[] = $stokCheck['message'];
            }

            $fasilitasData[] = [
                'fasilitas' => $fasilitas,
                'jumlah' => $item['jumlah_dipinjam'],
            ];
        }

        if (!empty($errors)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Validasi peminjaman gagal:<br>' . implode('<br>', $errors));
        }

        $oldStatus = $peminjaman->status_pinjam;

        DB::transaction(function () use ($validated, $peminjaman, $fasilitasData, $oldStatus) {
            $data = [
                'keperluan' => $validated['keperluan'],
                'catatan_peminjam' => $validated['catatan_peminjam'] ?? null,
                'waktu_mulai' => $validated['waktu_mulai'],
                'waktu_selesai' => $validated['waktu_selesai'],
                'status_pinjam' => 'menunggu_bikem', // Reset kembali ke menunggu_bikem
            ];

            if (request()->hasFile('file_pendukung')) {
                if ($peminjaman->file_pendukung && Storage::disk('public')->exists($peminjaman->file_pendukung)) {
                    Storage::disk('public')->delete($peminjaman->file_pendukung);
                }
                $file = request()->file('file_pendukung');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $data['file_pendukung'] = $file->storeAs('file_peminjaman', $filename, 'public');
            }

            $peminjaman->update($data);

            if ($oldStatus === 'perlu_revisi') {
                $peminjaman->logStatusChange('menunggu_bikem', 'perlu_revisi', 'Pemohon telah memperbarui pengajuan (revisi diajukan kembali ke BIKEM).');

                // Notifikasi ke BIKEM bahwa pemohon sudah mengirim revisi
                $bikemUsers = User::role('bikem')->get();
                $pemohonTitle = Auth::user()->hasRole('bikem') ? 'Petugas BIKEM (' . Auth::user()->nama . ')' : 'Mahasiswa ' . Auth::user()->nama;
                foreach ($bikemUsers as $bikem) {
                    $this->notificationService->create(
                        $bikem,
                        '🔄 Revisi Diajukan Kembali',
                        $pemohonTitle . ' telah memperbarui pengajuan #' . $peminjaman->id . ' sesuai catatan revisi.',
                        'booking_revision_resubmitted',
                        route('bikem.peminjaman.show', $peminjaman),
                        $peminjaman
                    );
                }
            }

            // Force delete item lama lalu insert yang baru
            $peminjaman->items()->forceDelete();

            foreach ($fasilitasData as $dataItem) {
                PeminjamanFasilitasItem::create([
                    'peminjaman_id' => $peminjaman->id,
                    'fasilitas_id' => $dataItem['fasilitas']->id,
                    'jumlah_dipinjam' => $dataItem['jumlah'],
                    'status_item' => 'menunggu_bikem',
                ]);
            }
        });

        $message = ($oldStatus === 'perlu_revisi')
            ? 'Revisi pengajuan peminjaman berhasil diajukan kembali ke BIKEM / Kemahasiswaan!'
            : 'Pengajuan peminjaman berhasil diperbarui!';

        return redirect()->route('pelapor.peminjaman.index')->with('success', $message);
    }

    public function destroy(PeminjamanFasilitas $peminjaman): RedirectResponse
    {
        if ($peminjaman->peminjam_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke peminjaman ini.');
        }

        if ($peminjaman->waktu_mulai <= now()) {
            return redirect()->route('pelapor.peminjaman.index')
                ->with('error', 'Peminjaman yang sudah memasuki hari pelaksanaan tidak dapat dihapus.');
        }

        if (!in_array($peminjaman->status_pinjam, ['pending', 'menunggu_bikem', 'perlu_revisi'])) {
            return redirect()->route('pelapor.peminjaman.index')
                ->with('error', 'Peminjaman yang sudah diproses tidak dapat dihapus.');
        }

        if ($peminjaman->file_pendukung && Storage::disk('public')->exists($peminjaman->file_pendukung)) {
            Storage::disk('public')->delete($peminjaman->file_pendukung);
        }

        DB::transaction(function () use ($peminjaman) {
            $peminjaman->items()->delete();
            $peminjaman->delete();
        });

        return redirect()->route('pelapor.peminjaman.index')
            ->with('success', 'Pengajuan peminjaman berhasil dihapus!');
    }

    public function cekStok(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'fasilitas_id' => 'required|exists:fasilitas,id',
            'waktu_mulai' => 'nullable|date',
            'waktu_selesai' => 'nullable|date',
            'exclude_peminjaman_id' => 'nullable|integer|exists:peminjaman_fasilitas,id'
        ]);

        $fasilitas = Fasilitas::find($request->fasilitas_id);
        $waktuMulai = $request->waktu_mulai;
        $waktuSelesai = $request->waktu_selesai;
        $excludeId = $request->exclude_peminjaman_id;

        // Ambil jadwal terpakai pada hari yang dipilih
        $schedules = [];
        if ($waktuMulai) {
            $schedules = $this->bookingService->getOccupiedSchedules($fasilitas->id, $waktuMulai, $excludeId);
        }

        $available = $fasilitas->stok_total;
        $isConflict = false;
        $message = 'Tersedia';

        if ($waktuMulai && $waktuSelesai) {
            $stokCheck = $this->bookingService->checkStokAvailability($fasilitas->id, 1, $waktuMulai, $waktuSelesai, $excludeId);
            $available = $fasilitas->getAvailableStock($waktuMulai, $waktuSelesai, $excludeId);
            $isConflict = !$stokCheck['available'];
            $message = $stokCheck['message'];
        }

        $totalUsed = $fasilitas->stok_total - $available;

        return response()->json([
            'success' => true,
            'available' => $available,
            'stok_total' => $fasilitas->stok_total,
            'total_used' => $totalUsed,
            'is_conflict' => $isConflict,
            'message' => $message,
            'kategori' => $fasilitas->kategori,
            'nama_fasilitas' => $fasilitas->nama_fasilitas,
            'schedules' => $schedules,
        ]);
    }

    public function cekSemuaStok(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
            'exclude_peminjaman_id' => 'nullable|integer|exists:peminjaman_fasilitas,id'
        ]);

        $waktuMulai = $request->waktu_mulai;
        $waktuSelesai = $request->waktu_selesai;
        $excludeId = $request->exclude_peminjaman_id;

        $fasilitas = Fasilitas::pinjam()
            ->whereIn('status_kondisi', ['baik', 'rusak_ringan'])
            ->orderBy('nama_fasilitas')
            ->get();

        $data = $fasilitas->map(function ($item) use ($waktuMulai, $waktuSelesai, $excludeId) {
            $stokCheck = $this->bookingService->checkStokAvailability($item->id, 1, $waktuMulai, $waktuSelesai, $excludeId);
            $available = $item->getAvailableStock($waktuMulai, $waktuSelesai, $excludeId);
            return [
                'id' => $item->id,
                'nama' => $item->nama_fasilitas,
                'kategori' => $item->kategori,
                'stok_total' => $item->stok_total,
                'available' => $available,
                'is_available' => $stokCheck['available'],
                'message' => $stokCheck['message'],
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}