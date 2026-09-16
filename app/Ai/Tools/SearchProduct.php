<?php

namespace App\Ai\Tools;

use App\Services\DedupGuardService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
        $keyword      = $request['keyword'];
        $requirements = $request['requirements'];
        $sources      = $request['sources'];
        $location     = $request['location'];

        $locationStr = collect([$location['block'] ?? null, $location['district'] ?? null, $location['state'] ?? null, $location['pincode'] ?? null])
            ->filter()->implode(', ');

        $dedupKey = "{$keyword}|" . implode(',', $sources) . "|{$locationStr}";
        if ($this->dedup->alreadyDone('search', $dedupKey)) {
            return "Already searched for '{$keyword}' — reuse earlier results instead of searching again.";
        }

        $reqText = implode(' ', $requirements);
        $q = trim("{$keyword} {$reqText} {$locationStr}");

        $requirementQueries = $this->buildRequirementQueries($keyword, $requirements, $locationStr);

        Log::info("SearchProduct: '{$q}' → " . count($requirementQueries) . " queries");
        return "jsddsj";

        // $responses = Http::pool(function ($pool) use ($requirementQueries) {
        //     $calls = [];
        //     foreach ($requirementQueries as $tag => $params) {
        //         $calls[] = $pool->as($tag)->get(config('ai.providers.serpapi.uri'), array_merge(
        //             ['api_key' => config('ai.providers.serpapi.key')],
        //             $params
        //         ));
        //     }
        //     return $calls;
        // });

        // $allResults = collect();

        // foreach ($requirementQueries as $tag => $params) {
        //     $body = $responses[$tag]->json();

        //     $items = match ($tag) {
        //         'photo'    => data_get($body, 'images_results', []),
        //         'video'    => data_get($body, 'video_results', []),
        //         'shopping' => data_get($body, 'shopping_results', []),
        //         'local'    => data_get($body, 'local_results', []),
        //         default    => data_get($body, 'organic_results', []),
        //     };

        //     Log::info("SearchProduct [{$tag}]: '{$params['q']}' → " . count($items) . " results");

        //     $allResults[$tag] = collect($items)->map(function ($r) use ($tag) {
        //         return match ($tag) {
        //             'photo'    => ['type' => 'photo', 'title' => $r['title'] ?? '', 'link' => $r['original'] ?? $r['thumbnail'] ?? ''],
        //             'video'    => ['type' => 'video', 'title' => $r['title'] ?? '', 'link' => $r['link'] ?? ''],
        //             'shopping' => ['type' => 'price', 'title' => $r['title'] ?? '', 'price' => $r['extracted_price'] ?? null, 'link' => $r['link'] ?? ''],
        //             'local'    => ['type' => 'seller', 'title' => $r['title'] ?? '', 'address' => $r['address'] ?? '', 'phone' => $r['phone'] ?? '', 'link' => $r['website'] ?? ''],
        //             default    => ['type' => $tag, 'title' => $r['title'] ?? '', 'link' => $r['link'] ?? ''],
        //         };
        //     });
        // }

        // $shoppingItems = data_get($responses['shopping']->json(), 'shopping_results', []);
        // $shoppingResults = collect($shoppingItems)->map(fn($r) => [
        //     'title'  => $r['title'] ?? '',
        //     'link'   => $r['link'] ?? '',
        //     'price'  => $r['extracted_price'] ?? null,
        //     'source' => 'google_shopping',
        // ]);

        // $this->dedup->markDone('search', $dedupKey);

        // return $this->rankAndFormat($allResults, $shoppingResults, $keyword);
    }

    protected function rankAndFormat(Collection $webResults, Collection $shoppingResults, string $keyword): string
    {
        $deduped = $webResults->unique(function ($r) {
            return parse_url($r['link'], PHP_URL_HOST) . parse_url($r['link'], PHP_URL_PATH);
        });

        $byUrl = $deduped->groupBy(fn($r) => parse_url($r['link'], PHP_URL_HOST));
        $scored = $deduped->map(function ($r) use ($byUrl) {
            $host = parse_url($r['link'], PHP_URL_HOST);
            $r['engine_count'] = $byUrl->get($host, collect())->pluck('source')->unique()->count();
            return $r;
        })->sortByDesc('engine_count');

        $output = collect();

        if ($shoppingResults->isNotEmpty()) {
            $output->push('--- Structured price data ---');
            $output = $output->merge($shoppingResults->map(fn($r) => "{$r['title']} — ₹{$r['price']} — {$r['link']}"));
        }

        $output->push('--- Web results (ranked by cross-engine agreement) ---');
        $output = $output->merge($scored->take(10)->map(fn($r) => "[{$r['source']}] {$r['title']} — {$r['link']}"));

        return $output->implode("\n") ?: 'No results found.';
    }

    protected function buildRequirementQueries(string $keyword, array $requirements, string $locationStr): array
    {
        $queries = [];

        if (in_array('photo', $requirements)) {
            $queries['photo'] = [
                'q' => $keyword,
                'engine' => 'google_images',
                'gl' => 'in',
            ];
        }

        if (in_array('video', $requirements) || in_array('video_link', $requirements)) {
            $queries['video'] = [
                'q' => "{$keyword} review",
                'engine' => 'google_videos',
                'gl' => 'in',
            ];
        }

        if (in_array('price', $requirements) || in_array('comparison', $requirements) || in_array('availability', $requirements)) {
            $queries['shopping'] = [
                'q' => $keyword,
                'engine' => 'google_shopping',
                'gl' => 'in',
            ];
        }

        if (in_array('feedback', $requirements)) {
            $queries['reviews'] = [
                'q' => "{$keyword} reviews ratings",
                'engine' => 'google',
                'gl' => 'in',
                'num' => 8,
            ];
        }

        if (in_array('seller', $requirements) || in_array('address', $requirements) || in_array('contact', $requirements)) {
            $queries['local'] = [
                'q' => "{$keyword} dealer distributor {$locationStr}",
                'engine' => 'google_maps',
                'gl' => 'in',
                'type' => 'search',
            ];
        }

        if (in_array('website', $requirements)) {
            $queries['official'] = [
                'q' => "{$keyword} official website",
                'engine' => 'google',
                'gl' => 'in',
                'num' => 3,
            ];
        }

        return $queries;
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'keyword'      => $schema->string()->required(),
            'requirements' => $schema->array()->items($schema->string())->required(),
            'sources'      => $schema->array()->items($schema->string())->required(),
            'location'     => $schema->object([
                'country'  => $schema->string(),
                'state'    => $schema->string(),
                'district' => $schema->string(),
                'block'    => $schema->string(),
                'pincode'  => $schema->string(),
            ])->required(),
        ];
    }
}
