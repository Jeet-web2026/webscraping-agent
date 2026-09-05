<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class DedupGuardService
{
    public function __construct(protected int $requestId) {}

    public function alreadyDone(string $action, string $key): bool
    {
        return Cache::has($this->cacheKey($action, $key));
    }

    public function markDone(string $action, string $key): void
    {
        // TTL matches job timeout — dedup only needs to last one run
        Cache::put($this->cacheKey($action, $key), true, now()->addMinutes(10));
    }

    protected function cacheKey(string $action, string $key): string
    {
        return "research:{$this->requestId}:{$action}:" . md5($key);
    }
}
