<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductUpdateRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // Get the product ID from the route parameter
        $productId = $this->route('product');
        
        // If it's a Product model instance, get the ID
        if ($productId instanceof \App\Models\Product) {
            $productId = $productId->id;
        }
        
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'name')->ignore($productId)
            ],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['required', 'boolean'],
            'type' => ['nullable', 'string', 'max:20'],
            'is_perishable' => ['nullable', 'boolean'],
            'hs_code' => ['nullable', 'string'],
        ];
    }
}
