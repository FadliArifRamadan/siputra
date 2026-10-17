<?php

namespace App\Http\Controllers\Bikem;

use App\Http\Controllers\Controller;
use App\Models\PeminjamanFasilitas;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PeminjamanBikemController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request): View
    {
        $status = $request->get('status', 'menunggu_bikem');
        $search = $request->get('search');

        $query = PeminjamanFasilitas::with(['peminjam', 'items.fasilitas'])
            ->orderBy('created_at', 'desc');

        // Filter status tab
        if ($status === 'menunggu_bikem') {
            $query->whereIn('status_pinjam', ['pending', 'menunggu_bikem']);
        } elseif ($status !== 'all' && in_array($status, ['perlu_revisi', 'menunggu_rtk', 'disetujui', 'ditolak', 'berjalan', 'selesai'])) {
            $query->where('status_pinjam', $status);
        }

        // Pencarian nama peminjam atau keperluan
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('keperluan', 'like', "%{$search}%")
                    ->orWhereHas('peminjam', function ($sub) use ($search) {
                        $sub->where('nama', 'like', "%{$search}%")
                            ->orWhere('nomor_induk', 'like', "%{$search}%");
                    });
            });
        }

        $peminjaman = $query->paginate(15)->withQueryString();

        // Counter untuk badge tabs
        $counts = [
            'menunggu_bikem' => PeminjamanFasilitas::whereIn('status_pinjam', ['pending', 'menunggu_bikem'])->count(),
            'perlu_revisi' => PeminjamanFasilitas::where('status_pinjam', 'perlu_revisi')->count(),
            'menunggu_rtk' => PeminjamanFasilitas::where('status_pinjam', 'menunggu_rtk')->count(),
            'disetujui' => PeminjamanFasilitas::where('status_pinjam', 'disetujui')->count(),
            'ditolak' => PeminjamanFasilitas::where('status_pinjam', 'ditolak')->count(),
            'all' => PeminjamanFasilitas::count(),
        ];

        return view('bikem.peminjaman.index', compact('peminjaman', 'status', 'search', 'counts'));
    }

    public function show(PeminjamanFasilitas $peminjaman): View
    {
        $peminjaman->load([
            'peminjam',
            'items.fasilitas.lokasi',
            'verifikasiBikemBy',
            'verifikasiSerahTerimaBy',
            'verifikasiPengembalianBy',
            'statusLogs.actor'
        ]);

        return view('bikem.peminjaman.show', compact('peminjaman'));
    }

    public function approve(Request $request, PeminjamanFasilitas $peminjaman): RedirectResponse
    {
        if (!in_array($peminjaman->status_pinjam, ['pending', 'menunggu_bikem', 'perlu_revisi'])) {
            return redirect()->back()->with('error', 'Pengajuan ini tidak dapat disetujui BIKEM pada status saat ini.');
        }

        $request->validate([
            'catatan' => 'nullable|string|max:500',
        ]);

        $catatan = $request->input('catatan');
        $oldStatus = $peminjaman->status_pinjam;

        DB::transaction(function () use ($peminjaman, $catatan, $oldStatus) {
            $peminjaman->update([
                'status_pinjam' => 'menunggu_rtk',
                'verifikasi_bikem_by' => Auth::id(),
                'verifikasi_bikem_at' => now(),
                'catatan_bikem' => $catatan,
            ]);

            $peminjaman->items()->update(['status_item' => 'menunggu_rtk']);

            $logMessage = 'Disetujui oleh BIKEM (' . Auth::user()->nama . ')' . ($catatan ? '. Catatan: ' . $catatan : '');
            $peminjaman->logStatusChange('menunggu_rtk', $oldStatus, $logMessage);

            // 1. Notifikasi ke peminjam (Pelapor)
            $this->notificationService->create(
                $peminjaman->peminjam,
                '✅ Peminjaman Disetujui BIKEM',
                'Pengajuan peminjaman Anda telah disetujui oleh BIKEM dan diteruskan ke RTK untuk verifikasi ketersediaan fasilitas.',
                'booking_bikem_approved',
                route('pelapor.peminjaman.show', $peminjaman),
                $peminjaman
            );

            // 2. Notifikasi ke semua RTK
            $rtkUsers = User::role('rtk')->get();
            foreach ($rtkUsers as $rtk) {
                $this->notificationService->create(
                    $rtk,
                    '📋 Pengajuan Siap Diverifikasi',
                    'Pengajuan peminjaman #' . $peminjaman->id . ' (' . $peminjaman->peminjam->nama . ') telah disetujui BIKEM dan siap diverifikasi RTK.',
                    'booking_ready_for_rtk',
                    route('rtk.peminjaman.show', $peminjaman),
                    $peminjaman
                );
            }
        });

        return redirect()->route('bikem.peminjaman.show', $peminjaman)
            ->with('success', 'Pengajuan peminjaman berhasil disetujui dan diteruskan ke RTK.');
    }

    public function requestRevision(Request $request, PeminjamanFasilitas $peminjaman): RedirectResponse
    {
        if (!in_array($peminjaman->status_pinjam, ['pending', 'menunggu_bikem', 'perlu_revisi', 'ditolak'])) {
            return redirect()->back()->with('error', 'Pengajuan ini tidak dalam status yang dapat diminta revisi.');
        }

        $request->validate([
            'catatan' => 'required|string|min:5|max:1000',
        ], [
            'catatan.required' => 'Catatan revisi wajib diisi agar pemohon mengetahui hal yang perlu diperbaiki.',
            'catatan.min' => 'Catatan revisi minimal 5 karakter.',
        ]);

        $catatan = $request->input('catatan');
        $oldStatus = $peminjaman->status_pinjam;

        DB::transaction(function () use ($peminjaman, $catatan, $oldStatus) {
            $peminjaman->update([
                'status_pinjam' => 'perlu_revisi',
                'verifikasi_bikem_by' => Auth::id(),
                'verifikasi_bikem_at' => now(),
                'catatan_bikem' => $catatan,
            ]);

            $peminjaman->items()->update(['status_item' => 'perlu_revisi']);

            $logMessage = 'Permintaan revisi oleh BIKEM: ' . $catatan;
            $peminjaman->logStatusChange('perlu_revisi', $oldStatus, $logMessage);

            // Notifikasi ke peminjam
            $this->notificationService->create(
                $peminjaman->peminjam,
                '⚠️ Pengajuan Peminjaman Perlu Revisi',
                'BIKEM meminta revisi pada pengajuan peminjaman Anda. Catatan: ' . $catatan,
                'booking_revision_requested',
                route('pelapor.peminjaman.show', $peminjaman),
                $peminjaman
            );
        });

        return redirect()->route('bikem.peminjaman.show', $peminjaman)
            ->with('success', 'Permintaan revisi telah dikirim ke pemohon. Pemohon sekarang dapat memperbaiki pengajuannya.');
    }

    public function reject(Request $request, PeminjamanFasilitas $peminjaman): RedirectResponse
    {
        if (!in_array($peminjaman->status_pinjam, ['pending', 'menunggu_bikem', 'perlu_revisi'])) {
            return redirect()->back()->with('error', 'Pengajuan ini tidak dalam status yang dapat ditolak BIKEM.');
        }

        $request->validate([
            'catatan' => 'required|string|min:5|max:1000',
        ], [
            'catatan.required' => 'Alasan penolakan wajib diisi.',
            'catatan.min' => 'Alasan penolakan minimal 5 karakter.',
        ]);

        $catatan = $request->input('catatan');
        $oldStatus = $peminjaman->status_pinjam;

        DB::transaction(function () use ($peminjaman, $catatan, $oldStatus) {
            $peminjaman->update([
                'status_pinjam' => 'ditolak',
                'verifikasi_bikem_by' => Auth::id(),
                'verifikasi_bikem_at' => now(),
                'catatan_bikem' => $catatan,
            ]);

            $peminjaman->items()->update(['status_item' => 'ditolak']);

            $logMessage = 'Ditolak oleh BIKEM: ' . $catatan;
            $peminjaman->logStatusChange('ditolak', $oldStatus, $logMessage);

            // Notifikasi ke peminjam
            $this->notificationService->create(
                $peminjaman->peminjam,
                '❌ Peminjaman Ditolak BIKEM',
                'Pengajuan peminjaman Anda ditolak oleh BIKEM. Alasan: ' . $catatan,
                'booking_bikem_rejected',
                route('pelapor.peminjaman.show', $peminjaman),
                $peminjaman
            );
        });

        return redirect()->route('bikem.peminjaman.show', $peminjaman)
            ->with('success', 'Pengajuan peminjaman berhasil ditolak.');
    }
}
