<?php

namespace App\Jobs;

use App\Helpers\SerpApiHelper;
use App\Models\ResearchRequest;
use App\Services\Products\SerpApiProductsQueryBuilder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;

class ResearchAgentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries = 1;
    public int $timeout = 180;

    public function __construct(protected int $requestId) {}

    public function handle(): void
    {
        $record = ResearchRequest::findOrFail($this->requestId);
        $record->update(['status' => 'processing']);

        try {
            $query = SerpApiProductsQueryBuilder::forProduct($record->subject, $record->filters);

            $response = SerpApiHelper::search($query);

            $record->update([
                'status' => 'initial_completed',
                'result' => ['result' =>  $response->json('local_results') ?? []],
            ]);
        } catch (\Throwable $e) {
            $record->update(['status' => 'failed']);
            throw $e;
        }
    }
}
