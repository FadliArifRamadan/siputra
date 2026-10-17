<?php

namespace App\Services\Dashboard;

use App\Models\Fasilitas;
use App\Models\PeminjamanFasilitas;
use App\Models\PengajuanKerusakan;
use App\Models\PenugasanTeknisi;
use App\Models\User;
use Carbon\Carbon;
use App\Services\SlaService;
use Illuminate\Support\Facades\DB;

class RtkDashboardService extends BaseDashboardService
{
    protected SlaService $slaService;
    public function __construct()
    {
        parent::__construct('dashboard_rtk', 300); // 5 menit cache
        $this->slaService = app(SlaService::class);
    }

    protected function fetchData(int $userId): array
    {
        // ==========================================
        // 1. WIDGET "BUTUH TINDAKAN SEKARANG"
        // ==========================================
        $pendingAccounts = User::where('status_akun', 'pending')
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'rtk');
            })
            ->count();

        $pendingLaporan = PengajuanKerusakan::where('status_laporan', 'pending')->count();

        $pendingPeminjaman = PeminjamanFasilitas::where('status_pinjam', 'pending')->count();

        $needAction = [
            'pending_accounts' => $pendingAccounts,
            'pending_laporan' => $pendingLaporan,
            'pending_peminjaman' => $pendingPeminjaman,
            'total' => $pendingAccounts + $pendingLaporan + $pendingPeminjaman,
        ];

        // ==========================================
        // 2. STATISTIK RINGKAS
        // ==========================================
        $totalLaporan = PengajuanKerusakan::count();
        $laporanSelesai = PengajuanKerusakan::where('status_laporan', 'selesai')->count();
        $laporanDiproses = PengajuanKerusakan::where('status_laporan', 'diproses')->count();
        $laporanPending = PengajuanKerusakan::where('status_laporan', 'pending')->count();
        $laporanDitolak = PengajuanKerusakan::where('status_laporan', 'ditolak')->count();

        // Rata-rata waktu penyelesaian (dalam hari)
        $avgCompletionTime = $this->calculateAverageCompletionTime();

        $stats = [
            'total_laporan' => $totalLaporan,
            'laporan_selesai' => $laporanSelesai,
            'laporan_diproses' => $laporanDiproses,
            'laporan_pending' => $laporanPending,
            'laporan_ditolak' => $laporanDitolak,
            'completion_rate' => $totalLaporan > 0 ? round(($laporanSelesai / $totalLaporan) * 100, 1) : 0,
            'avg_completion_days' => $avgCompletionTime,
        ];

        // ==========================================
        // 3. LINE CHART: Tren Laporan per Bulan (12 bulan terakhir)
        // ==========================================
        $monthlyTrend = $this->getMonthlyTrend();

        // ==========================================
        // 4. PIE CHART: Distribusi Status Laporan
        // ==========================================
        $statusDistribution = [
            'pending' => $laporanPending,
            'diproses' => $laporanDiproses,
            'selesai' => $laporanSelesai,
            'ditolak' => $laporanDitolak,
        ];

        // ==========================================
        // 5. BAR CHART: Distribusi Tingkat Urgensi
        // ==========================================
        $urgensiDistribution = [
            'rendah' => PengajuanKerusakan::where('tingkat_urgensi', 'rendah')->count(),
            'sedang' => PengajuanKerusakan::where('tingkat_urgensi', 'sedang')->count(),
            'tinggi' => PengajuanKerusakan::where('tingkat_urgensi', 'tinggi')->count(),
        ];

        // ==========================================
        // 6. TOP 5 FASILITAS DENGAN LAPORAN TERBANYAK
        // ==========================================
        $topFacilities = Fasilitas::select('fasilitas.id', 'fasilitas.nama_fasilitas', DB::raw('COUNT(pengajuan_kerusakan.id) as total_laporan'))
            ->leftJoin('pengajuan_kerusakan', 'fasilitas.id', '=', 'pengajuan_kerusakan.fasilitas_id')
            ->whereNotNull('pengajuan_kerusakan.fasilitas_id')
            ->groupBy('fasilitas.id', 'fasilitas.nama_fasilitas')
            ->orderByDesc('total_laporan')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'nama' => $item->nama_fasilitas,
                    'total' => $item->total_laporan,
                ];
            })
            ->toArray();

        // ==========================================
        // 7. BEBAN KERJA PER TEKNISI
        // ==========================================
        $teknisiWorkload = User::role('teknisi')
            ->where('status_akun', 'active')
            ->withCount(['penugasanTeknisi as total_tugas' => function ($query) {
                $query->whereHas('pengajuanKerusakan', function ($q) {
                    $q->where('status_laporan', 'diproses');
                });
            }])
            ->get()
            ->map(function ($user) {
                return [
                    'nama' => $user->nama,
                    'total_tugas_aktif' => $user->total_tugas,
                ];
            })
            ->toArray();



        // ==========================================
        // 8. SLA BREACHED & WARNING REPORTS
        // ==========================================
        $slaBreached = $this->slaService->getBreachedReports(5);
        $slaWarning = $this->slaService->getWarningReports(5);

        $slaData = [
            'breached' => $slaBreached->map(function ($item) {
                return [
                    'id' => $item->id,
                    'jenis_kerusakan' => $item->jenis_kerusakan,
                    'pelapor' => $item->pelapor->nama ?? 'Tidak diketahui',
                    'target_selesai' => $item->target_selesai->format('d/m/Y H:i'),
                    'status' => $item->status_laporan,
                    'link' => route('pelapor.pengajuan.show', $item),
                ];
            })->toArray(),
            'warning' => $slaWarning->map(function ($item) {
                return [
                    'id' => $item->id,
                    'jenis_kerusakan' => $item->jenis_kerusakan,
                    'pelapor' => $item->pelapor->nama ?? 'Tidak diketahui',
                    'target_selesai' => $item->target_selesai->format('d/m/Y H:i'),
                    'status' => $item->status_laporan,
                    'link' => route('pelapor.pengajuan.show', $item),
                ];
            })->toArray(),
            'breached_count' => $slaBreached->count(),
            'warning_count' => $slaWarning->count(),
        ];

        return [
            'need_action' => $needAction,
            'stats' => $stats,
            'monthly_trend' => $monthlyTrend,
            'status_distribution' => $statusDistribution,
            'urgensi_distribution' => $urgensiDistribution,
            'top_facilities' => $topFacilities,
            'teknisi_workload' => $teknisiWorkload,
            'sla' => $slaData,  // ✅ TAMBAHKAN
        ];
    }

    private function calculateAverageCompletionTime(): ?float
    {
        // Cari laporan yang sudah selesai dan memiliki status log (ada riwayat status)
        $completedReports = PengajuanKerusakan::where('status_laporan', 'selesai')
            ->whereHas('statusLogs', function ($query) {
                $query->where('new_status', 'selesai');
            })
            ->with(['statusLogs' => function ($query) {
                $query->where('new_status', 'selesai')->orderBy('created_at', 'asc');
            }])
            ->get();

        if ($completedReports->isEmpty()) {
            return null;
        }

        $totalDays = 0;
        $count = 0;

        foreach ($completedReports as $report) {
            // Cari log pertama (status awal) dan log selesai
            $firstLog = $report->statusLogs()->orderBy('created_at', 'asc')->first();
            $completedLog = $report->statusLogs()->where('new_status', 'selesai')->first();

            if ($firstLog && $completedLog) {
                $days = $firstLog->created_at->diffInDays($completedLog->created_at);
                $totalDays += $days;
                $count++;
            }
        }

        return $count > 0 ? round($totalDays / $count, 1) : null;
    }

    private function getMonthlyTrend(): array
    {
        $months = collect();
        $now = Carbon::now();

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months->push([
                'month' => $month->format('M Y'),
                'month_num' => $month->month,
                'year' => $month->year,
            ]);
        }

        return $months->map(function ($item) {
            $count = PengajuanKerusakan::whereMonth('created_at', $item['month_num'])
                ->whereYear('created_at', $item['year'])
                ->count();

            return [
                'month' => $item['month'],
                'total' => $count,
            ];
        })->toArray();
    }
}
