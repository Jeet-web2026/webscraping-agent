<?php

namespace App\Ai\Agents;

use App\Ai\Tools\FetchWebPage;
use App\Ai\Tools\SearchProduct;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\MaxSteps;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Promptable;
use Stringable;

#[MaxSteps(6)]
#[Timeout(90)]
class ResearchAgent implements Agent, HasTools, HasStructuredOutput
{
    use Promptable;

    public function instructions(): Stringable|string
    {
        return <<<'PROMPT'
            You are a research agent. Given a subject and user-provided details,
            use SearchProduct to find relevant pages, then FetchWebPage to read
            the most promising 1-3 results.

            Rules:
            - Never repeat the same search query or fetch the same URL twice —
              if a tool tells you it's already done, use what you already have.
            - Only report facts actually found on a fetched page.
            - Leave a field empty if not found — never guess.
            - Maximum 3 page fetches total.
            PROMPT;
    }

    public function tools(): iterable
    {
        return [
            app(SearchProduct::class),
            app(FetchWebPage::class),
        ];
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'subject' => $schema->string()->required(),
            'summary' => $schema->string(),
            'details' => $schema->object([
                'prices' => $schema->array()->items(
                    $schema->object([
                        'source' => $schema->string(),
                        'value' => $schema->string(),
                        'url' => $schema->string(),
                    ])
                ),
                'contact' => $schema->object([
                    'name' => $schema->string(),
                    'address' => $schema->string(),
                    'phone' => $schema->string(),
                    'email' => $schema->string(),
                ]),
            ]),
            'sources_used' => $schema->array()->items($schema->string()),
        ];
    }
}
