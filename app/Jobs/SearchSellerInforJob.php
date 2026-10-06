<?php

namespace App\Jobs;

use App\Models\ResearchRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SearchSellerInforJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected ResearchRequest $researchRequest,
        protected string $sellerId
    ) {}

    public function handle(): void
    {
        try {
            $details = Http::timeout(120)
                ->retry(3, 100)
                ->get(config('services.scrapedoapi.base_url') . '/amazon/seller', [
                    'token' => config('services.scrapedoapi.api_key'),
                    'seller' => $this->sellerId,
                    'geocode' => 'IN',
                    'super' => true,
                ])
                ->throw()
                ->json();
        } catch (\Throwable $e) {
            Log::warning("Seller fetch failed for seller {$this->sellerId}: " . $e->getMessage());
            return;
        }

        $namePart = !empty($details['name']) ? $details['name'] : null;
        $businessTypePart = !empty($details['business_type']) ? ' (business type: ' . $details['business_type'] . ')' : null;
        $tradeNoPart = !empty($details['trade_register_number']) ? ' (trade no: ' . $details['trade_register_number'] . ')' : null;

        $sellerName = trim($namePart . $businessTypePart . $tradeNoPart);

        $phonePart = !empty($details['phone_number']) ? 'Phone: ' . $details['phone_number'] : null;
        $emailPart = !empty($details['email']) ? 'Email: ' . $details['email'] : null;

        $contactDetails = implode(', ', array_filter([$phonePart, $emailPart]));

        $address = !empty($details['about']) ? $details['about'] : null;

        $existing = $this->researchRequest->fresh()->result ?? [];
        $items = $existing['result'] ?? [];

        foreach ($items as &$item) {
            if (($item['seller_id'] ?? null) === $this->sellerId) {
                $item['seller_name'] = $sellerName;
                $item['contact_details'] = $contactDetails;
                $item['address'] = $address;
            }
        }
        unset($item);

        $this->researchRequest->update([
            'result' => array_merge($existing, ['result' => $items]),
        ]);
    }
}
