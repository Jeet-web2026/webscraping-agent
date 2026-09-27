<?php

namespace App\Jobs;

use App\Helpers\SerpApiHelper;
use App\Models\ProductdetailsResponse;
use App\Models\ResearchRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchProductWebDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private ResearchRequest $research,
        private array $request
    ) {}

    public function handle(): void
    {
        foreach ($this->request['requirements'] as $requirement) {
            if ($requirement === 'photo') {
                $query = $this->imagesSearchQuery();

                $response = SerpApiHelper::search($query);

                ProductdetailsResponse::query()->updateOrCreate([
                    'research_request_id' => $this->research->id
                ], [
                    'recent_photo' => collect($response['images_results'] ?? [])->pluck('thumbnail')->toArray()
                ]);

                $this->research->update([
                    'status' => 'completed'
                ]);
            }
        }
    }

    private function imagesSearchQuery(): array
    {
        return [
            "engine" => "google_images",
            "q" => $this->request['product_name'],
            "imgar" => "s",
            "google_domain" => "google.co.in",
            "gl" => "in",
            "hl" => "en",
            "location" => $this->request['country']
        ];
    }
}
