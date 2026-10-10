<?php

namespace App\Jobs;

use App\Interfaces\ApiTokenRepositoryInterface;
use App\Models\ResearchRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class ResearchAgentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries = 1;
    public int $timeout = 300;

    public function __construct(protected int $requestId) {}

    public function handle(ApiTokenRepositoryInterface $apiTokenInterface): void
    {
        $record = ResearchRequest::findOrFail($this->requestId);
        $record->update(['status' => 'processing']);

        try {
            $query = $record->user_prompt;

            $url = config('services.scrapedoapi.base_url') . '/google/maps/search' . '?token=' . $apiTokenInterface->findById(2)->token . '&q=' . urlencode($query);

            $response = Http::timeout(120)
                ->retry(3, 100)
                ->get($url)->throw()
                ->json();

            $record->update([
                'status' => 'initial_completed',
                'result' => ['result' =>  $response['local_results'] ?? []],
            ]);
        } catch (\Throwable $e) {
            $record->update(['status' => 'failed']);
            throw $e;
        }
    }
}
