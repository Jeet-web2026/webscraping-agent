<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:seller,service',
            'product_name' => 'required|string|max:255',
            'product_category' => 'nullable|string|max:255',
            'country' => 'required|string',
            'state' => 'required|string',
            'district' => 'required|string',
            'block' => 'required|string',
            'pincode' => 'nullable|digits:6',
            'source' => 'required|string',
        ];
    }
}
