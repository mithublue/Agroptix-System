<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QualityTestStoreRequest extends BaseFormRequest
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
            'batch_id' => ['nullable', 'integer', 'exists:batches,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'parameter_tested' => ['nullable', 'json'],
            'result' => ['nullable', 'string', 'max:100'],
            'result_status' => ['nullable', 'json'],
            'test_date' => ['nullable', 'date'],
            'lab_name' => ['nullable', 'string', 'max:255'],
            'test_certificate' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
