<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class ModelFailoverManager
{
    public function availableCandidates(string $chainName): array
    {
        $chain = config("ai.providers.chains.{$chainName}", []);

        return collect($chain)
            ->reject(fn($c) => $this->isCoolingDown($c['provider'], $c['model']))
            ->values()
            ->all();
    }

    public function markUnavailable(string $provider, string $model, int $seconds = 120): void
    {
        Cache::put($this->key($provider, $model), true, $seconds);
    }

    public function isCoolingDown(string $provider, string $model): bool
    {
        return Cache::has($this->key($provider, $model));
    }

    protected function key(string $provider, string $model): string
    {
        return "ai:cooldown:{$provider}:{$model}";
    }
}
