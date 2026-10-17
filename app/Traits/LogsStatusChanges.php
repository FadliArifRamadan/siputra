<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait LogsStatusChanges
{
    /**
     * Boot the trait to automatically log status changes.
     */
    protected static function bootLogsStatusChanges()
    {
        static::updated(function ($model) {
            // Skip logging if model is being soft-deleted (handled by deleting event)
            if ($model->isForceDeleting() || $model->trashed()) {
                return;
            }

            // Check if any status field changed
            $statusFields = ['status_laporan', 'status_pinjam', 'status_akun', 'status_kondisi'];
            
            foreach ($statusFields as $field) {
                if ($model->wasChanged($field)) {
                    $oldValue = $model->getOriginal($field);
                    $newValue = $model->{$field};
                    
                    // Log if both values are set and different
                    if ($oldValue !== $newValue) {
                        $model->logStatusChange(
                            $newValue,
                            $oldValue,
                            $model->catatan_rtk ?? $model->keterangan_selesai ?? null,
                            Auth::id()
                        );
                    }
                }
            }
        });

        // Log soft delete as a status change
        static::deleting(function ($model) {
            // ✅ Cek apakah model menggunakan SoftDeletes
            $usesSoftDeletes = in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive($model));
            
            if ($usesSoftDeletes && !$model->isForceDeleting()) {
                // ✅ PERBAIKAN: Gunakan static:: alih-alih $this->
                $statusField = static::getStatusField($model); 
                if ($statusField) {
                    $oldValue = $model->{$statusField};
                    $model->logStatusChange(
                        'deleted',
                        $oldValue,
                        'Data dihapus (soft delete) oleh ' . (Auth::user()?->nama ?? 'sistem'),
                        Auth::id()
                    );
                }
            }
        });

        // Log restore as a status change
        static::restored(function ($model) {
            // ✅ PERBAIKAN: Gunakan static:: alih-alih $this->
            $statusField = static::getStatusField($model); 
            if ($statusField) {
                $newValue = $model->{$statusField};
                $model->logStatusChange(
                    $newValue,
                    'deleted',
                    'Data dipulihkan (restore) oleh ' . (Auth::user()?->nama ?? 'sistem'),
                    Auth::id()
                );
            }
        });
    }

    /**
     * Get the status field name for this model.
     */
    protected static function getStatusField($model): ?string
    {
        $statusFields = ['status_laporan', 'status_pinjam', 'status_akun', 'status_kondisi'];
        foreach ($statusFields as $field) {
            if (in_array($field, $model->getFillable()) || in_array($field, array_keys($model->getAttributes()))) {
                return $field;
            }
        }
        return null;
    }
}