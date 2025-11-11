<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductStoreRequest extends BaseFormRequest
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
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('products', 'name')],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'type' => ['nullable', 'string', 'max:20'],
            'is_perishable' => ['nullable', 'boolean'],
            'hs_code' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        if (Auth()->has('price')) {
            $this->merge([
                'price' => (float) str_replace([',', '$'], '', $this->price)
            ]);
        }

        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'is_perishable' => $this->boolean('is_perishable')
        ]);
    }
}
