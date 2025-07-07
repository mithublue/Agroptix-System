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
            'batch_id' => ['nullable', 'integer', 'exists:batches,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'parameter_tested' => ['nullable', 'text', 'max:50'],
            'result' => ['nullable', 'string', 'max:100'],
            'result_status' => ['nullable', 'text', 'max:10'],
//            'batch_id' => ['required', 'integer', 'exists:batches,id'],
//            'user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
