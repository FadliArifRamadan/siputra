<?php

namespace App\Services;

use App\Models\PengajuanKerusakan;
use Carbon\Carbon;

class SlaService
{
    /**
     * Durasi target penyelesaian berdasarkan tingkat urgensi (dalam jam).
     */
    public function getTargetHours(string $tingkatUrgensi): int
    {
        return match ($tingkatUrgensi) {
            'tinggi' => 24,   // 1 hari
            'sedang' => 72,   // 3 hari
            'rendah' => 168,  // 7 hari
            default => 72,
        };
    }

    /**
     * Hitung target selesai berdasarkan created_at dan urgensi.
     */
    public function calculateTargetSelesai(Carbon $createdAt, string $tingkatUrgensi): Carbon
    {
        $hours = $this->getTargetHours($tingkatUrgensi);
        return $createdAt->copy()->addHours($hours);
    }

    /**
     * Tentukan status SLA berdasarkan target dan waktu sekarang.
     */
    public function getSlaStatus(Carbon $targetSelesai): string
    {
        $now = Carbon::now();
        
        if ($targetSelesai->isPast()) {
            return 'breached';
        }
        
        // Warning jika kurang dari 6 jam lagi
        if ($now->diffInHours($targetSelesai) <= 6) {
            return 'warning';
        }
        
        return 'on_track';
    }

    /**
     * Update SLA status untuk satu laporan.
     */
    public function updateSlaStatus(PengajuanKerusakan $pengajuan): void
    {
        if (!$pengajuan->target_selesai) {
            return;
        }

        // Hanya update jika laporan belum selesai
        if (in_array($pengajuan->status_laporan, ['selesai', 'ditolak'])) {
            return;
        }

        $status = $this->getSlaStatus($pengajuan->target_selesai);
        
        if ($pengajuan->sla_status !== $status) {
            $pengajuan->update(['sla_status' => $status]);
            
            // Jika breach, catat escalated_at
            if ($status === 'breached' && is_null($pengajuan->escalated_at)) {
                $pengajuan->update(['escalated_at' => now()]);
                
                // Log ke status_logs
                $pengajuan->logStatusChange(
                    'breached',
                    $pengajuan->sla_status,
                    'SLA breached: target selesai ' . $pengajuan->target_selesai->format('d/m/Y H:i')
                );
            }
        }
    }

    /**
     * Update SLA status untuk semua laporan yang perlu dicek.
     */
    public function updateAllSlaStatus(): void
    {
        $pengajuan = PengajuanKerusakan::whereIn('status_laporan', ['pending', 'diproses'])
            ->whereNotNull('target_selesai')
            ->get();

        foreach ($pengajuan as $item) {
            $this->updateSlaStatus($item);
        }
    }

    /**
     * Dapatkan laporan yang SLA-nya breached (untuk dashboard RTK).
     */
    public function getBreachedReports(int $limit = 10)
    {
        return PengajuanKerusakan::with(['pelapor', 'fasilitas'])
            ->where('sla_status', 'breached')
            ->whereIn('status_laporan', ['pending', 'diproses'])
            ->orderBy('target_selesai', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Dapatkan laporan yang SLA-nya warning (untuk dashboard RTK).
     */
    public function getWarningReports(int $limit = 10)
    {
        return PengajuanKerusakan::with(['pelapor', 'fasilitas'])
            ->where('sla_status', 'warning')
            ->whereIn('status_laporan', ['pending', 'diproses'])
            ->orderBy('target_selesai', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Reset SLA status saat laporan selesai atau diproses.
     */
    public function clearSlaStatus(PengajuanKerusakan $pengajuan): void
    {
        if ($pengajuan->sla_status !== 'on_track') {
            $pengajuan->update([
                'sla_status' => 'on_track',
                'escalated_at' => null,
            ]);
        }
    }
}