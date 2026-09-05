<?php

namespace App\Ai\Tools;

use App\Services\DedupGuardService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Http;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SearchProduct implements Tool
{
    public function __construct(protected DedupGuardService $dedup) {}

    public function description(): Stringable|string
    {
        return 'Searches the web for the given query and returns result titles/URLs.';
    }

    public function handle(Request $request): Stringable|string
    {
        $query = $request['query'];

        if ($this->dedup->alreadyDone('search', $query)) {
            return "Already searched for '{$query}' — reuse earlier results instead of searching again.";
        }

        $response = Http::get('https://serpapi.com/search', [
            'q' => $query,
            'api_key' => config('services.serpapi.key'),
            'num' => 5,
        ]);

        $this->dedup->markDone('search', $query);

        return collect($response->json('organic_results', []))
            ->map(fn($r) => "{$r['title']} — {$r['link']}")
            ->implode("\n") ?: 'No results found.';
    }

    public function schema(JsonSchema $schema): array
    {
        return ['query' => $schema->string()->required()];
    }
}
