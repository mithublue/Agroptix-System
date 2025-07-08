<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QualityTestStoreRequest extends FormRequest
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
            'batch_id' => ['required', 'integer', 'exists:batches,id'],
            'parameters_tested' => ['required', 'array', 'min:1'],
            'parameters_tested.*' => ['required', 'string', 'max:255'],
            'final_pass_fail' => ['required', 'in:pass,fail,pending'],
            'test_certificate' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
