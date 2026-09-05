<?php

namespace App\Ai\Tools;

use App\Services\DedupGuardService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Http;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class FetchWebPage implements Tool
{
    protected array $blockedHosts = ['google.com', 'bing.com', 'yahoo.com', 'duckduckgo.com'];

    public function __construct(protected DedupGuardService $dedup) {}

    public function description(): Stringable|string
    {
        return 'Fetches the text content of a specific URL (never a search engine URL — use SearchProduct for searching).';
    }

    public function handle(Request $request): Stringable|string
    {
        $url = $request['url'];
        $host = parse_url($url, PHP_URL_HOST) ?? '';

        if (collect($this->blockedHosts)->contains(fn($b) => str_contains($host, $b))) {
            return "Refused: {$host} is a search engine. Use SearchProduct instead.";
        }

        if ($this->dedup->alreadyDone('fetch', $url)) {
            return "Already fetched {$url} — reuse the earlier content instead of fetching again.";
        }

        $response = Http::timeout(15)->get($url);

        if ($response->failed()) {
            return "Failed to fetch {$url}: HTTP {$response->status()}";
        }

        $this->dedup->markDone('fetch', $url);

        $text = preg_replace('/\s+/', ' ', strip_tags($response->body()));
        return mb_substr($text, 0, 6000);
    }

    public function schema(JsonSchema $schema): array
    {
        return ['url' => $schema->string()->required()];
    }
}
