<?php

namespace App\Services\Dashboard;

use App\Models\PeminjamanFasilitas;
use App\Models\PengajuanKerusakan;

class PelaporDashboardService extends BaseDashboardService
{
    public function __construct()
    {
        parent::__construct('dashboard_pelapor', 120); // 2 menit cache
    }

    protected function fetchData(int $userId): array
    {
        // ==========================================
        // 1. RINGKASAN STATUS LAPORAN
        // ==========================================
        $laporan = PengajuanKerusakan::where('pelapor_id', $userId)->get();

        $laporanStats = [
            'total' => $laporan->count(),
            'pending' => $laporan->where('status_laporan', 'pending')->count(),
            'diproses' => $laporan->where('status_laporan', 'diproses')->count(),
            'selesai' => $laporan->where('status_laporan', 'selesai')->count(),
            'ditolak' => $laporan->where('status_laporan', 'ditolak')->count(),
        ];

        // ==========================================
        // 2. RINGKASAN STATUS PEMINJAMAN
        // ==========================================
        $peminjaman = PeminjamanFasilitas::where('peminjam_id', $userId)->get();

        $peminjamanStats = [
            'total' => $peminjaman->count(),
            'pending' => $peminjaman->where('status_pinjam', 'pending')->count(),
            'disetujui' => $peminjaman->where('status_pinjam', 'disetujui')->count(),
            'ditolak' => $peminjaman->where('status_pinjam', 'ditolak')->count(),
            'berjalan' => $peminjaman->where('status_pinjam', 'berjalan')->count(),
            'selesai' => $peminjaman->where('status_pinjam', 'selesai')->count(),
        ];

        // ==========================================
        // 3. LINIMASA 5 AKTIVITAS TERAKHIR
        // ==========================================
        $timeline = collect();

        $formatStatus = function (?string $status) {
            if (!$status) return 'Awal';
            return match (strtolower($status)) {
                'menunggu_bikem' => 'Menunggu BIKEM',
                'perlu_revisi' => 'Perlu Revisi',
                'menunggu_rtk' => 'Menunggu RTK',
                'disetujui' => 'Disetujui',
                'ditolak' => 'Ditolak',
                'berjalan' => 'Berjalan',
                'selesai' => 'Selesai',
                'pending' => 'Pending',
                'diproses' => 'Diproses',
                default => ucwords(str_replace('_', ' ', $status)),
            };
        };

        // Ambil status log terakhir dari laporan milik user
        $laporanIds = PengajuanKerusakan::where('pelapor_id', $userId)->pluck('id');
        if ($laporanIds->isNotEmpty()) {
            $laporanLogs = \App\Models\StatusLog::where('loggable_type', PengajuanKerusakan::class)
                ->whereIn('loggable_id', $laporanIds)
                ->with(['actor', 'loggable.fasilitas'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            foreach ($laporanLogs as $log) {
                $laporan = $log->loggable;
                $titleText = $laporan?->jenis_kerusakan ?? ($laporan?->fasilitas?->nama_fasilitas ?? 'Kerusakan Fasilitas');

                $timeline->push([
                    'type' => 'laporan',
                    'title' => 'Laporan: ' . $titleText,
                    'message' => 'Status berubah dari ' . $formatStatus($log->old_status) . ' → ' . $formatStatus($log->new_status),
                    'actor' => $log->actor->nama ?? 'Sistem',
                    'link' => route('pelapor.pengajuan.show', $log->loggable_id),
                    'created_at' => $log->created_at,
                    'loggable_id' => $log->loggable_id,
                    'old_status' => $log->old_status,
                    'new_status' => $log->new_status,
                ]);
            }
        }

        // Ambil status log terakhir dari peminjaman milik user
        $peminjamanIds = PeminjamanFasilitas::where('peminjam_id', $userId)->pluck('id');
        if ($peminjamanIds->isNotEmpty()) {
            $peminjamanLogs = \App\Models\StatusLog::where('loggable_type', PeminjamanFasilitas::class)
                ->whereIn('loggable_id', $peminjamanIds)
                ->with(['actor', 'loggable.items.fasilitas'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            foreach ($peminjamanLogs as $log) {
                /** @var PeminjamanFasilitas|null $peminjaman */
                $peminjaman = $log->loggable;
                $titleText = $peminjaman ? $peminjaman->nama_fasilitas : 'Fasilitas';

                $timeline->push([
                    'type' => 'peminjaman',
                    'title' => 'Peminjaman: ' . $titleText,
                    'message' => 'Status berubah dari ' . $formatStatus($log->old_status) . ' → ' . $formatStatus($log->new_status),
                    'actor' => $log->actor->nama ?? 'Sistem',
                    'link' => route('pelapor.peminjaman.show', $log->loggable_id),
                    'created_at' => $log->created_at,
                    'loggable_id' => $log->loggable_id,
                    'old_status' => $log->old_status,
                    'new_status' => $log->new_status,
                ]);
            }
        }

        // Deduplicate jika ada log kembar (karena double-logging dalam waktu bersamaan)
        $timeline = $timeline->unique(function ($item) {
            return $item['type'] . '_' . $item['loggable_id'] . '_' . $item['old_status'] . '_' . $item['new_status'] . '_' . $item['created_at']->format('Y-m-d H:i');
        });

        // Sort by created_at desc and take 5
        $timeline = $timeline->sortByDesc('created_at')->take(5)->values()->map(function ($item) {
            $item['time_ago'] = $item['created_at']->diffForHumans();
            return $item;
        });

        return [
            'laporan_stats' => $laporanStats,
            'peminjaman_stats' => $peminjamanStats,
            'timeline' => $timeline->toArray(),
        ];
    }
}