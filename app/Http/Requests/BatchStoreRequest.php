<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class BatchStoreRequest extends BaseFormRequest
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

    protected function prepareForValidation()
    {
        // Log the raw input for debugging
        \Log::debug('Raw input data:', $this->all());

        // Process inputs
        $this->merge([
            'batch_code' => trim($this->batch_code ?? ''),
            'has_defect' => (bool) ($this->has_defect ?? false),
        ]);

        // Log the processed data
        \Log::debug('Processed data:', $this->all());
    }

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
            'fair_trade_premium' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:3'],
        ];
    }

    public function messages()
    {
        return [
            'harvest_time.required' => 'The harvest time field is required.',
            'harvest_time.date' => 'The harvest time must be a valid date.',
        ];
    }
}
