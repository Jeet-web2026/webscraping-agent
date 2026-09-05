<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchProductWebDataJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $prompt = <<<'PROMPT'
Tell me briefly about Ashirvaad Atta.
Return JSON only.
PROMPT;

        $response = Http::timeout(60)
            ->withHeaders([
                'x-goog-api-key' => config('ai.providers.gemini.key'),
                'Content-Type' => 'application/json',
            ])
            ->post(
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent',
                [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => $prompt,
                                ],
                            ],
                        ],
                    ],
                ]
            );

        if ($response->failed()) {
            Log::error('Gemini request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return;
        }

        $data = $response->json();

        Log::info('Gemini response', [
            'response' => $data,
        ]);

        $text = data_get(
            $data,
            'candidates.0.content.parts.0.text'
        );

        Log::info('Gemini text', [
            'text' => $text,
        ]);
    }
}
