<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BatchStoreRequest extends FormRequest
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
            'batch_code' => ['nullable', 'string'],
            'source_id' => ['nullable', 'integer', 'exists:sources,,id'],
            'product_id' => ['nullable', 'integer', 'exists:products,,id'],
            'harvest_time' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:20'],
            'source_as_source_id' => ['required', 'integer', 'exists:source_as_sources,id'],
            'product_as_product_id' => ['required', 'integer', 'exists:product_as_products,id'],
        ];
    }
}
