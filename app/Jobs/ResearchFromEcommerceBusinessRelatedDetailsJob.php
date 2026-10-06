<?php

namespace App\Jobs;

use App\Models\ResearchRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class ResearchFromEcommerceBusinessRelatedDetailsJob implements ShouldQueue
{
    use Queueable;


    public function __construct(private readonly ResearchRequest $researchRequest) {}

    public function handle(): void
    {
        if ($this->researchRequest->status === 'initial_completed') {
            $existing = $this->researchRequest->result ?? [];

            foreach ($existing['result'] ?? [] as $item) {
                $detailedResult = Http::timeout(120)
                    ->retry(3, 100)
                    ->get(config('services.scrapedoapi.base_url') . '/amazon/pdp', [
                        'token'   => config('services.scrapedoapi.api_key'),
                        'asin'    => $item['asin'],
                        'geocode' => 'IN',
                    ])
                    ->throw()
                    ->json();

                $sellerId = $detailedResult['third_party_seller']['id'] ?? null;

                if (!empty($sellerId)) {
                    SearchSellerInforJob::dispatch($this->researchRequest, $sellerId);
                }
            }
        }
    }
}
