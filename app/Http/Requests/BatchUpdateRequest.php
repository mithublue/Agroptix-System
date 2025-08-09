<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BatchUpdateRequest extends BaseFormRequest
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
            'producer_id' => ['required', 'integer', 'exists:users,id'],
            'source_id' => [
                'required',
                'integer',
                Rule::exists('sources', 'id')->where(function ($q) {
                    $q->where('owner_id', (int) request('producer_id'));
                }),
            ],
            'product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id'),
                Rule::exists('product_user', 'product_id')->where(function ($q) {
                    $q->where('user_id', (int) request('producer_id'));
                }),
            ],
            'harvest_time' => ['required', 'date'],
            'status' => ['nullable', 'string', 'in:' . implode(',', array_keys(\App\Models\Batch::STATUSES))],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'grade' => ['nullable', 'string', 'in:' . implode(',', array_keys(\App\Models\Batch::GRADES))],
            'has_defect' => ['sometimes', 'boolean'],
            'remark' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
