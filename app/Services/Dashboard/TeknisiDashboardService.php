<?php

namespace App\Services\Dashboard;

use App\Models\PenugasanTeknisi;
use Carbon\Carbon;

class TeknisiDashboardService extends BaseDashboardService
{
    public function __construct()
    {
        parent::__construct('dashboard_teknisi', 180); // 3 menit cache
    }

    protected function fetchData(int $userId): array
    {
        // ==========================================
        // 1. STATISTIK PRIBADI
        // ==========================================
        $totalTugas = PenugasanTeknisi::where('teknisi_id', $userId)->count();

        $tugasAktif = PenugasanTeknisi::where('teknisi_id', $userId)
            ->whereHas('pengajuanKerusakan', function ($query) {
                $query->where('status_laporan', 'diproses');
            })
            ->count();

        $tugasSelesaiBulanIni = PenugasanTeknisi::where('teknisi_id', $userId)
            ->whereHas('pengajuanKerusakan', function ($query) {
                $query->where('status_laporan', 'selesai')
                    ->whereMonth('updated_at', Carbon::now()->month)
                    ->whereYear('updated_at', Carbon::now()->year);
            })
            ->count();

        $avgCompletionTime = $this->calculatePersonalAverageCompletionTime($userId);

        $stats = [
            'total_tugas' => $totalTugas,
            'tugas_aktif' => $tugasAktif,
            'tugas_selesai_bulan_ini' => $tugasSelesaiBulanIni,
            'avg_completion_days' => $avgCompletionTime,
        ];

        // ==========================================
        // 2. TUGAS PRIORITAS (urgensi tinggi + mendekati tenggat)
        // ==========================================
        $prioritasTugas = PenugasanTeknisi::where('teknisi_id', $userId)
            ->whereHas('pengajuanKerusakan', function ($query) {
                $query->where('status_laporan', 'diproses');
            })
            ->with(['pengajuanKerusakan' => function ($query) {
                $query->with('pelapor');
            }])
            ->orderBy('jadwal_penanganan', 'asc')
            ->limit(5)
            ->get()
            ->map(function ($tugas) {
                $isOverdue = $tugas->jadwal_penanganan < now();
                $daysUntilDeadline = $isOverdue ? 0 : now()->diffInDays($tugas->jadwal_penanganan, false);

                return [
                    'id' => $tugas->id,
                    'jenis_kerusakan' => $tugas->pengajuanKerusakan->jenis_kerusakan,
                    'lokasi' => $tugas->pengajuanKerusakan->lokasi_detail,
                    'pelapor' => $tugas->pengajuanKerusakan->pelapor->nama ?? 'Tidak diketahui',
                    'tingkat_urgensi' => $tugas->pengajuanKerusakan->tingkat_urgensi,
                    'jadwal' => $tugas->jadwal_penanganan->format('d/m/Y H:i'),
                    'is_overdue' => $isOverdue,
                    'days_until_deadline' => $daysUntilDeadline,
                    'link' => route('teknisi.tugas.show', $tugas),
                ];
            })
            ->toArray();

        // ==========================================
        // 3. JUMLAH TUGAS MELEWATI TENGGAT (SLA breach)
        // ==========================================
        $overdueCount = PenugasanTeknisi::where('teknisi_id', $userId)
            ->whereHas('pengajuanKerusakan', function ($query) {
                $query->where('status_laporan', 'diproses');
            })
            ->where('jadwal_penanganan', '<', now())
            ->count();

        return [
            'stats' => $stats,
            'prioritas_tugas' => $prioritasTugas,
            'overdue_count' => $overdueCount,
        ];
    }

    private function calculatePersonalAverageCompletionTime(int $userId): ?float
    {
        $completedTasks = PenugasanTeknisi::where('teknisi_id', $userId)
            ->whereHas('pengajuanKerusakan', function ($query) {
                $query->where('status_laporan', 'selesai');
            })
            ->with(['pengajuanKerusakan' => function ($query) {
                $query->with(['statusLogs' => function ($q) {
                    $q->where('new_status', 'selesai')->orderBy('created_at', 'asc');
                }]);
            }])
            ->get();

        if ($completedTasks->isEmpty()) {
            return null;
        }

        $totalDays = 0;
        $count = 0;

        foreach ($completedTasks as $task) {
            $report = $task->pengajuanKerusakan;
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
}