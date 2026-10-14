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
            'geolocation_search' => 'nullable|boolean',

            'country'  => 'required_without:geolocation_search|nullable|string',
            'state'    => 'required_without:geolocation_search|nullable|string',
            'district' => 'required_without:geolocation_search|nullable|string',
            'pincode'  => 'required_without:geolocation_search|nullable|digits:6',
            'block'    => 'nullable|string',
            
            'source' => 'sometimes|string',
        ];
    }
}
