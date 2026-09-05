<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->input('type')) {
            'product' => [
                'type' => 'required|in:product',
                'product_name' => 'required|string|max:255',
                'product_keyword' => 'nullable|string|max:255',
                'country' => 'nullable|string',
                'state' => 'nullable|string',
                'district' => 'nullable|string',
                'block' => 'nullable|string',
                'pincode' => 'nullable|digits:6',
                'requirements' => 'nullable|array',
                'sources' => 'nullable|array',
                'instructions' => 'nullable|string|max:2000',
            ],
            'service' => [
                'type' => 'required|in:service',
                'service_name' => 'required|string|max:255',
                'service_pincode' => 'nullable|digits:6',
                'service_state' => 'nullable|string',
                'service_district' => 'nullable|string',
                'service_block' => 'nullable|string',
                'service_radius' => 'nullable|string',
                'service_requirements' => 'nullable|array',
            ],
            'customer' => [
                'type' => 'required|in:customer',
                'customer_id' => 'nullable|string',
                'customer_name' => 'nullable|string',
                'customer_mobile' => 'nullable|string',
                'customer_actions' => 'nullable|array',
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from',
                'question' => 'nullable|string|max:1000',
            ],
            default => ['type' => 'required|in:product,service,customer'],
        };
    }
}
