<?php

namespace App\Jobs;


use App\Models\ResearchRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class ResearchFromEcommerceJob implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 300;

    public function __construct(
        private readonly int $researchRequestId,
        private readonly ?string $category = null,
        private readonly ?string $pincode = null,
    ) {}

    public function handle(): void
    {
        $record = ResearchRequest::findOrFail($this->researchRequestId);

        try {
            $record->update(['status' => 'processing']);

            $params = array_filter([
                'token'    => config('services.scrapedoapi.api_key'),
                'category' => $this->category,
                'geocode'  => 'in',
                'type'     => 'bestsellers',
                'page'     => 1,
                'zipcode'  => $this->pincode,
            ]);

            $response = Http::timeout(120)
                ->retry(3, 100)
                ->get(config('services.scrapedoapi.base_url') . '/amazon/bestsellers', $params)
                ->throw()
                ->json();

            $record->update([
                'status' => 'initial_completed',
                'result' => ['result' => $response['products'] ?? []],
            ]);
        } catch (\Throwable $e) {
            $record->update(['status' => 'failed']);
            throw $e;
        }
    }
}
