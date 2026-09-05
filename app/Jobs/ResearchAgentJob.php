<?php

namespace App\Jobs;

use App\Ai\Agents\ResearchAgent;
use App\Models\ResearchRequest;
use App\Services\DedupGuardService;
use App\Services\ModelFailoverManager;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ResearchAgentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 180;

    public function __construct(protected int $requestId) {}

    public function handle(ModelFailoverManager $failover): void
    {
        $record = ResearchRequest::findOrFail($this->requestId);
        $record->update(['status' => 'researching']);

        app()->instance(DedupGuardService::class, new DedupGuardService($this->requestId));

        $candidates = $failover->availableCandidates('research');

        if (empty($candidates)) {
            $record->update(['status' => 'failed', 'error' => 'All models are currently cooling down. Try again shortly.']);
            return;
        }

        $prompt = "Subject: {$record->subject}\nFilters: " . json_encode($record->filters)
            . "\nUser request: {$record->user_prompt}";

        foreach ($candidates as $candidate) {
            try {
                $response = (new ResearchAgent)->prompt(
                    $prompt,
                    provider: $candidate['provider'],
                    model: $candidate['model'],
                );

                $record->update([
                    'status' => 'researched',
                    'model_used' => "{$candidate['provider']}:{$candidate['model']}",
                    'result' => $response,
                ]);

                DocBuilderJob::dispatch($record->id);
                return; // success — stop trying other models

            } catch (Throwable $e) {
                Log::warning("Model {$candidate['provider']}:{$candidate['model']} failed: {$e->getMessage()}");

                if ($this->looksLikeRateLimitOrOverload($e)) {
                    $failover->markUnavailable($candidate['provider'], $candidate['model']);
                }
            }
        }

        $record->update(['status' => 'failed', 'error' => 'All configured models failed for this request.']);
    }

    protected function looksLikeRateLimitOrOverload(Throwable $e): bool
    {
        return (bool) preg_match('/(overloaded|rate.?limit|429|503|quota)/i', $e->getMessage());
    }
}
