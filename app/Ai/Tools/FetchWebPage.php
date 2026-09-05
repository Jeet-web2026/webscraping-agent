<?php

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Http;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class FetchWebPage implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Fetches the text content of a given URL. Use this to read product pages, price pages, or seller info pages.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $response = Http::timeout(15)->get($request['url']);
        dd($response->status(), $response->body());

        if ($response->failed()) {
            return "Failed to fetch {$request['url']}: HTTP {$response->status()}";
        }

        $text = strip_tags($response->body());
        $text = preg_replace('/\s+/', ' ', $text);

        return mb_substr($text, 0, 6000);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'url' => $schema->string()->required(),
        ];
    }
}
