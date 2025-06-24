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
            'batch_code' => ['nullable', 'string', 'max:255'],
            'source_id' => ['nullable', 'integer', 'exists:sources,id'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'harvest_time' => ['required', 'date'],
            'status' => ['required', 'string', 'in:' . implode(',', array_keys(\App\Models\Batch::STATUSES))],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'grade' => ['nullable', 'string', 'in:' . implode(',', array_keys(\App\Models\Batch::GRADES))],
            'has_defect' => ['boolean'],
            'remark' => ['nullable', 'string', 'max:1000']
        ];
    }
}
