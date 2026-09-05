<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAiRequest;
use App\Jobs\ResearchAgentJob;
use App\Models\ResearchRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class ResearchRequestController extends Controller
{
    public function store(StoreAiRequest $request): JsonResponse|RedirectResponse
    {
        $data = $request->validated();

        $compacted = match ($data['type']) {
            'product' => $this->compactProduct($data),
            'service' => $this->compactService($data),
            'customer' => $this->compactCustomer($data),
        };

        $record = ResearchRequest::create([
            'user_id' => $request->user()->id ?? 1,
            'subject' => $compacted['subject'],
            'filters' => $compacted['filters'],
            'user_prompt' => $compacted['user_prompt'],
            'status' => 'pending',
        ]);

        $data['type'] === 'customer'
            ? null
            : ResearchAgentJob::dispatch($record->id);

        return response()->json([
            'id' => $record->id,
            'status' => $record->status,
            'message' => 'Request submitted — processing now.',
        ], 202);
    }

    protected function compactProduct(array $d): array
    {
        return [
            'subject' => $d['product_name'],
            'filters' => [
                'type' => 'product',
                'keyword' => $d['product_keyword'] ?? null,
                'location' => array_filter([
                    'country' => $d['country'] ?? null,
                    'state' => $d['state'] ?? null,
                    'district' => $d['district'] ?? null,
                    'block' => $d['block'] ?? null,
                    'pincode' => $d['pincode'] ?? null,
                ]),
                'requirements' => $d['requirements'] ?? [],
                'sources' => $d['sources'] ?? [],
            ],
            'user_prompt' => $this->buildPrompt(
                "Find product information for: {$d['product_name']}",
                $d['product_keyword'] ?? null,
                $d['requirements'] ?? [],
                $d['instructions'] ?? null,
            ),
        ];
    }

    protected function compactService(array $d): array
    {
        return [
            'subject' => $d['service_name'],
            'filters' => [
                'type' => 'service',
                'location' => array_filter([
                    'state' => $d['service_state'] ?? null,
                    'district' => $d['service_district'] ?? null,
                    'block' => $d['service_block'] ?? null,
                    'pincode' => $d['service_pincode'] ?? null,
                    'radius' => $d['service_radius'] ?? null,
                ]),
                'requirements' => $d['service_requirements'] ?? [],
            ],
            'user_prompt' => $this->buildPrompt(
                "Find service providers for: {$d['service_name']}",
                $d['service_pincode'] ?? null,
                $d['service_requirements'] ?? [],
            ),
        ];
    }

    protected function compactCustomer(array $d): array
    {
        return [
            'subject' => $d['customer_name'] ?? $d['customer_id'] ?? 'Customer',
            'filters' => [
                'type' => 'customer',
                'customer_id' => $d['customer_id'] ?? null,
                'customer_mobile' => $d['customer_mobile'] ?? null,
                'actions' => $d['customer_actions'] ?? [],
                'date_from' => $d['date_from'] ?? null,
                'date_to' => $d['date_to'] ?? null,
            ],
            'user_prompt' => $d['question'] ?? 'Provide a summary based on the selected actions.',
        ];
    }

    protected function buildPrompt(string $base, ?string $extra, array $requirements, ?string $instructions = null): string
    {
        $parts = [$base];

        if ($extra) {
            $parts[] = "Keyword/PIN: {$extra}";
        }

        if (!empty($requirements)) {
            $parts[] = 'Include: ' . implode(', ', $requirements);
        }

        if ($instructions) {
            $parts[] = "Additional instructions: {$instructions}";
        }

        return implode(". ", $parts);
    }

    public function show(ResearchRequest $researchRequest): JsonResponse
    {
        return response()->json([
            'status' => $researchRequest->status,
            'model_used' => $researchRequest->model_used,
            'result' => $researchRequest->result,
            'error' => $researchRequest->error,
            'download_url' => $researchRequest->generated_file_path
                ? $researchRequest->generated_file_path
                : null,
        ]);
    }
}
