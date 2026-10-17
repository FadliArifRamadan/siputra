<?php

namespace App\Services\Dashboard;

use Illuminate\Support\Facades\Cache;

abstract class BaseDashboardService
{
    protected string $cacheTag;
    protected int $cacheTTL;

    public function __construct(string $cacheTag, int $cacheTTL = 300)
    {
        $this->cacheTag = $cacheTag;
        $this->cacheTTL = $cacheTTL;
    }

    /**
     * Get dashboard data with caching.
     */
    public function getData(int $userId): array
    {
        $cacheKey = $this->getCacheKey($userId);
        
      return Cache::remember($cacheKey, $this->cacheTTL, function () use ($userId) {
    return $this->fetchData($userId);
});
    }

    /**
     * Get cache key for specific user.
     */
    protected function getCacheKey(int $userId): string
    {
        return $this->cacheTag . '_user_' . $userId;
    }

    /**
     * Fetch fresh dashboard data.
     */
    abstract protected function fetchData(int $userId): array;
}