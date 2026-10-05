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
            $results  = [];

            foreach ($existing['result'] ?? [] as $item) {
                $detailedResult = Http::timeout(120)
                    ->retry(3, 100)
                    ->get(config('services.scrapedoapi.base_url') . '/amazon/pdp', [
                        'token'    => config('services.scrapedoapi.api_key'),
                        'asin' => $item['asin'],
                        'geocode' => "IN",
                    ])
                    ->throw()
                    ->json();

                $sellerId = $detailedResult['third_party_seller']['id'] ?? null;

                $details = Http::timeout(120)
                    ->retry(3, 100)
                    ->get(config('services.scrapedoapi.base_url') . '/amazon/seller', [
                        'token'    => config('services.scrapedoapi.api_key'),
                        'seller' => $sellerId,
                        'geocode' => "IN",
                    ])
                    ->throw()
                    ->json();

                $item['seller_name'] = $details['business_name'] ?? null . ' (business type:' . $details['business_type'] ?? null . '), (trade no:' . $details['trade_register_number'] ?? null . ')';
                $item['contact_details']        =  "Phone:" . $details['phone_number'] ?? null . ", Email: " . $details['email'] ?? null;
                $item['address'] = $details['business_address'];

                $results[] = $item;
            }

            $this->researchRequest->update([
                'result' => array_merge($existing, ['result' => $results]),
            ]);
        }
    }
}
