<?php

namespace App\Jobs;

use App\Models\ResearchRequest;
use App\Services\SerpApiQueryBuilder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use SerpApi\Client;

class ResearchAgentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 180;

    public function __construct(protected int $requestId) {}

    public function handle(): void
    {
        $record = ResearchRequest::findOrFail($this->requestId);
        $record->update(['status' => 'processing']);

        try {
            $query = SerpApiQueryBuilder::forProduct($record->subject, $record->filters);
            Log::info([$query]);
            $query['api_key'] = config('ai.providers.serpapi.key');

            $response = Http::get('https://serpapi.com/search.json', $query);

            if ($response->failed()) {
                throw new RuntimeException("SerpApi error: {$response->body()}");
            }

            $localResults = $response->json('local_results') ?? [];
            Log::info([$localResults]);
            $record->update([
                'status' => 'completed',
                'result' => ['result' => $localResults],
            ]);

            $record->update(['status' => 'completed']);
        } catch (\Throwable $e) {
            $record->update(['status' => 'failed']);
            throw $e;
        }
    }
}
