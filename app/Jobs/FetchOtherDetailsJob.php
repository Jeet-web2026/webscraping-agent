<?php

namespace App\Jobs;

use App\Interfaces\ApiTokenRepositoryInterface;
use App\Models\ResearchRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class FetchOtherDetailsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly ResearchRequest $researchRequest) {}

    public function handle(ApiTokenRepositoryInterface $apiTokenInterface): void
    {
        if ($this->researchRequest->status === 'initial_completed') {
            $existing = $this->researchRequest->result ?? [];
            $results  = [];

            foreach ($existing['result'] ?? [] as $item) {
                $detailedResult = Http::timeout(120)
                    ->retry(3, 100)
                    ->get(config('services.scrapedoapi.base_url') . '/google/maps/place', [
                        'token'    => $apiTokenInterface->findById(2)->token,
                        'place_id' => $item['place_id'],
                    ])
                    ->throw()
                    ->json();

                $place = $detailedResult['place_results'] ?? [];

                $item['reviews'] = collect(data_get($place, 'user_reviews.most_relevant', []))
                    ->values()
                    ->map(fn($r, $i) => sprintf(
                        '%d. name: %s, ratings: %s, (%s)',
                        $i + 1,
                        $r['username'] ?? '',
                        $r['rating'] ?? '',
                        $r['description'] ?? ''
                    ))
                    ->implode("\n");
                $item['price']        =  $place['price'] ?? null;

                $results[] = $item;
            }

            $this->researchRequest->update([
                'result' => array_merge($existing, ['result' => $results]),
            ]);
        }
    }
}
