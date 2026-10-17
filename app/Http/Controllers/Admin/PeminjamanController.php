<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\PeminjamanDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePeminjamanStatusRequest;
use App\Models\PeminjamanFasilitas;
use App\Models\PeminjamanFasilitasItem;
use App\Models\User;
use App\Services\BookingValidationService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    protected NotificationService $notificationService;
    protected BookingValidationService $bookingService;

    public function __construct(
        NotificationService $notificationService,
        BookingValidationService $bookingService
    ) {
        $this->notificationService = $notificationService;
        $this->bookingService = $bookingService;
    }

    public function index(PeminjamanDataTable $dataTable)
    {
        return $dataTable->render('rtk.peminjaman.index');
    }

    public function approve(UpdatePeminjamanStatusRequest $request, PeminjamanFasilitas $peminjaman): RedirectResponse
    {
        if (!in_array($peminjaman->status_pinjam, ['menunggu_rtk', 'pending'])) {
            return redirect()->back()
                ->with('error', 'Peminjaman ini belum disetujui oleh BIKEM atau sudah diproses sebelumnya.');
        }

        // ✅ FIX: Load relasi items dan fasilitas untuk menghindari N+1 & Null Exception
        $peminjaman->load('items.fasilitas');

        // Validasi stok untuk setiap item (Multi-item support)
        $errors = [];
        foreach ($peminjaman->items as $item) {
            $stokCheck = $this->bookingService->checkStokAvailability(
                $item->fasilitas_id,
                $item->jumlah_dipinjam,
                $peminjaman->waktu_mulai->toDateTimeString(),
                $peminjaman->waktu_selesai->toDateTimeString(),
                $peminjaman->id
            );

            if (!$stokCheck['available']) {
                $errors[] = ($item->fasilitas->nama_fasilitas ?? 'Fasilitas') . ': ' . $stokCheck['message'];
            }
        }

        if (!empty($errors)) {
            return redirect()->back()
                ->with('error', 'Tidak dapat menyetujui:<br>' . implode('<br>', $errors));
        }

        $oldStatus = $peminjaman->status_pinjam;
        $newStatus = $request->status_pinjam;
        $catatan = $request->input('catatan');

        DB::transaction(function () use ($peminjaman, $newStatus, $oldStatus, $catatan) {
            // Update header status
            $peminjaman->update(['status_pinjam' => $newStatus]);
            $peminjaman->logStatusChange($newStatus, $oldStatus, $catatan);

            // Update semua item statusnya mengikuti header
            $peminjaman->items()->update(['status_item' => $newStatus]);

            if ($newStatus === 'disetujui' && $peminjaman->waktu_mulai <= now()) {
                $peminjaman->update(['status_pinjam' => 'berjalan']);
                $peminjaman->items()->update(['status_item' => 'berjalan']);
                $peminjaman->logStatusChange('berjalan', 'disetujui', 'Otomatis berjalan karena waktu mulai sudah lewat');
            }

            // Notifikasi ke peminjam
            $statusLabel = $newStatus === 'disetujui' ? 'disetujui' : 'ditolak';
            $sendEmail = $newStatus === 'disetujui';

            $this->notificationService->notify(
                $peminjaman->peminjam,
                'Peminjaman ' . ucfirst($statusLabel),
                'Pengajuan peminjaman Anda telah ' . $statusLabel . ' oleh RTK.',
                'booking_' . $statusLabel,
                route('pelapor.peminjaman.show', $peminjaman),
                $peminjaman,
                $sendEmail
            );
        });

        return redirect()->route('rtk.peminjaman.index')
            ->with('success', 'Peminjaman berhasil ' . ($newStatus === 'disetujui' ? 'disetujui' : 'ditolak') . '.');
    }

    public function reject(Request $request, PeminjamanFasilitas $peminjaman): RedirectResponse
    {
        $request->validate([
            'catatan' => 'nullable|string|max:500',
        ]);

        if (!in_array($peminjaman->status_pinjam, ['menunggu_rtk', 'pending'])) {
            return redirect()->back()
                ->with('error', 'Peminjaman ini belum siap diproses RTK atau sudah diproses sebelumnya.');
        }

        DB::transaction(function () use ($peminjaman, $request) {
            $oldStatus = $peminjaman->status_pinjam;
            $peminjaman->update(['status_pinjam' => 'ditolak']);
            $peminjaman->items()->update(['status_item' => 'ditolak']);
            $peminjaman->logStatusChange('ditolak', $oldStatus, $request->input('catatan'));

            // Notifikasi ke peminjam
            $this->notificationService->notify(
                $peminjaman->peminjam,
                'Peminjaman Ditolak',
                'Pengajuan peminjaman Anda ditolak oleh RTK.' . ($request->input('catatan') ? ' Alasan: ' . $request->input('catatan') : ''),
                'booking_rejected',
                route('pelapor.peminjaman.show', $peminjaman),
                $peminjaman
            );
        });

        return redirect()->route('rtk.peminjaman.index')
            ->with('success', 'Peminjaman berhasil ditolak.');
    }

    public function start(PeminjamanFasilitas $peminjaman): RedirectResponse
    {
        if ($peminjaman->status_pinjam !== 'disetujui') {
            return redirect()->back()
                ->with('error', 'Peminjaman harus disetujui terlebih dahulu.');
        }

        if ($peminjaman->waktu_mulai > now()) {
            return redirect()->back()
                ->with('error', 'Waktu mulai peminjaman belum tiba.');
        }

        DB::transaction(function () use ($peminjaman) {
            $oldStatus = $peminjaman->status_pinjam;
            $peminjaman->update(['status_pinjam' => 'berjalan']);
            $peminjaman->items()->update(['status_item' => 'berjalan']);
            $peminjaman->logStatusChange('berjalan', $oldStatus, 'Peminjaman dimulai.');
        });

        return redirect()->route('rtk.peminjaman.index')
            ->with('success', 'Peminjaman berhasil dimulai.');
    }

    public function complete(PeminjamanFasilitas $peminjaman): RedirectResponse
    {
        if ($peminjaman->status_pinjam !== 'berjalan') {
            return redirect()->back()
                ->with('error', 'Peminjaman harus berstatus berjalan.');
        }

        DB::transaction(function () use ($peminjaman) {
            $oldStatus = $peminjaman->status_pinjam;
            $peminjaman->update(['status_pinjam' => 'selesai']);
            $peminjaman->items()->update(['status_item' => 'selesai']);
            $peminjaman->logStatusChange('selesai', $oldStatus, 'Peminjaman selesai.');
        });

        return redirect()->route('rtk.peminjaman.index')
            ->with('success', 'Peminjaman berhasil ditandai selesai.');
    }

    public function show(PeminjamanFasilitas $peminjaman): View
    {
        $peminjaman->load([
            'peminjam',
            'items.fasilitas',
            'statusLogs.actor',
            'verifikasiBikemBy',
            'verifikasiSerahTerimaBy',
            'verifikasiPengembalianBy'
        ]);

        return view('rtk.peminjaman.show', compact('peminjaman'));
    }
}