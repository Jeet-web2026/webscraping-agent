<?php

namespace App\Jobs;

use App\Interfaces\ApiTokenRepositoryInterface;
use App\Models\ResearchRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class ResearchFromEcommerceBusinessRelatedDetailsJob implements ShouldQueue
{
    use Queueable;


    public function __construct(private readonly ResearchRequest $researchRequest) {}

    public function handle(ApiTokenRepositoryInterface $apiTokenInterface): void
    {
        if ($this->researchRequest->status === 'initial_completed') {
            $existing = $this->researchRequest->result ?? [];
            $results   = [];
            $sellerIds = [];

            foreach ($existing['result'] ?? [] as $item) {
                $detailedResult = Http::timeout(120)
                    ->retry(3, 100)
                    ->get(config('services.scrapedoapi.base_url') . '/amazon/pdp', [
                        'token'   => $apiTokenInterface->findById(2)->token,
                        'asin'    => $item['asin'],
                        'geocode' => 'IN',
                    ])
                    ->throw()
                    ->json();

                $sellerId = $detailedResult['third_party_seller']['id'] ?? null;

                $item['seller_id'] = $sellerId;

                if (!empty($sellerId)) {
                    $sellerIds[$sellerId] = true;
                }

                $results[] = $item;
            }

            $this->researchRequest->update([
                'result' => array_merge($existing, ['result' => $results]),
            ]);

            foreach (array_keys($sellerIds) as $sellerId) {
                SearchSellerInforJob::dispatch($this->researchRequest, $sellerId);
            }

            $this->researchRequest->update([
                'status' => 'completed'
            ]);
        }
    }
}
