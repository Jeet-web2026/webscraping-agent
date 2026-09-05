<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\MaxSteps;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;

#[MaxSteps(2)]
class DocFormatterAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function instructions(): Stringable|string
    {
        return <<<'PROMPT'
            You reformat raw research data into clean, document-ready fields.
            Do not invent any new facts — only rephrase/clean what's given.
            Keep the summary to 2-3 sentences, plain professional tone.
            PROMPT;
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'title' => $schema->string()->required(),
            'summary' => $schema->string()->required(),
            'sections' => $schema->array()->items(
                $schema->object([
                    'heading' => $schema->string(),
                    'body' => $schema->string(),
                ])
            ),
        ];
    }
}
