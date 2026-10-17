<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait HasDashboardCache
{
    /**
     * Boot the trait to clear dashboard cache on model changes.
     */
    protected static function bootHasDashboardCache()
    {
        // Clear cache when model is created, updated, or deleted
        static::saved(function ($model) {
            $model->clearDashboardCache();
        });

        static::deleted(function ($model) {
            $model->clearDashboardCache();
        });

        // Also clear when soft-deleted or restored
        if (method_exists(static::class, 'bootSoftDeletes')) {
            static::restored(function ($model) {
                $model->clearDashboardCache();
            });
        }
    }

    /**
     * Clear all dashboard cache tags.
     */
 public function clearDashboardCache(): void
{
    Cache::flush();
}
}