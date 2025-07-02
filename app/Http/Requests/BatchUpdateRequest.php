<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BatchUpdateRequest extends FormRequest
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
            'batch_code' => ['required', 'string', 'max:255'],
            'source_id' => ['required', 'integer', 'exists:sources,id'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'harvest_time' => ['required', 'date'],
            'status' => ['required', 'string', 'max:20'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'grade' => ['nullable', 'string', 'max:50'],
            'has_defect' => ['boolean'],
            'remark' => ['nullable', 'string'],
        ];
    }
}
