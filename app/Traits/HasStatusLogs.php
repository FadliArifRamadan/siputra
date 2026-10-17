<?php

namespace App\Traits;

use App\Models\StatusLog;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasStatusLogs
{
    /**
     * Get all status logs for this model.
     */
    public function statusLogs(): MorphMany
    {
        return $this->morphMany(StatusLog::class, 'loggable');
    }

    public function logStatusChange(string $newStatus, ?string $oldStatus = null, ?string $keterangan = null, ?int $actorId = null): StatusLog
    {
        // If oldStatus not provided, try to get from current attribute
        if ($oldStatus === null && $this->exists) {
            $oldStatus = $this->getOriginal('status_laporan') ?? 
                         $this->getOriginal('status_pinjam') ?? 
                         $this->getOriginal('status_akun') ?? 
                         null;
        }

        $actorId = $actorId ?? auth()->id();

        // Mencegah duplicate log jika event Eloquent (LogsStatusChanges) & pemanggilan manual di controller berjalan bersamaan
        $recentLog = $this->statusLogs()
            ->where('old_status', $oldStatus)
            ->where('new_status', $newStatus)
            ->where('created_at', '>=', now()->subSeconds(15))
            ->latest('id')
            ->first();

        if ($recentLog) {
            // Update keterangan atau actor jika log baru membawa info lebih spesifik
            if (!empty($keterangan) && empty($recentLog->keterangan)) {
                $recentLog->update([
                    'keterangan' => $keterangan,
                    'actor_id' => $actorId ?? $recentLog->actor_id,
                ]);
            }
            return $recentLog;
        }

        return $this->statusLogs()->create([
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'actor_id' => $actorId,
            'keterangan' => $keterangan,
        ]);
    }

    /**
     * Get latest status log.
     */
    public function getLatestStatusLog(): ?StatusLog
    {
        return $this->statusLogs()->latest()->first();
    }

    /**
     * Get status timeline (all logs ordered by time).
     */
    public function getStatusTimeline(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->statusLogs()->with('actor')->orderBy('created_at', 'asc')->get();
    }
}