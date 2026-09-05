<?php

namespace App\Ai\Agents;

use App\Ai\Tools\FetchWebPage;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\MaxSteps;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Laravel\Ai\Providers\Tools\ProviderTool;
use Stringable;

#[Provider(Lab::Gemini)]
#[Model('gemini-3.8-flash')]
#[MaxSteps(6)]
#[Timeout(90)]
class ScrapDataFromInternet implements Agent, Conversational, HasTools
{
    use Promptable;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return <<<'PROMPT'
            You are a product research agent. Given a product name and any
            region/category filters, search the web, fetch the most relevant
            pages, and extract factual information about the product.

            Rules:
            - Only report facts you actually found on a fetched page.
            - If a field is not found, leave it empty — do not guess or invent data.
            - Prefer official seller/manufacturer pages and well-known retailers.
            - Limit yourself to a maximum of 3 page fetches.
            PROMPT;
    }

    /**
     * Get the list of messages comprising the conversation so far.
     *
     * @return Message[]
     */
    public function messages(): iterable
    {
        return [];
    }

    /**
     * Get the tools available to the agent.
     *
     * @return list<Agent|Tool|ProviderTool>
     */
    public function tools(): iterable
    {
        return [
            new FetchWebPage
        ];
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'product_name' => $schema->string()->required(),
            'description' => $schema->string(),
            'prices' => $schema->array()->items(
                $schema->object([
                    'source' => $schema->string(),
                    'price' => $schema->string(),
                    'url' => $schema->string(),
                ])
            ),
            'seller' => $schema->object([
                'name' => $schema->string(),
                'address' => $schema->string(),
                'contact' => $schema->string(),
            ]),
            'sources_used' => $schema->array()->items($schema->string()),
        ];
    }
}
