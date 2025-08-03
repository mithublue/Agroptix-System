<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QualityTestUpdateRequest extends BaseFormRequest
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
            'batch_id' => ['nullable', 'integer', 'exists:batches,,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,,id'],
            'parameter_tested' => ['nullable', 'string', 'max:50'],
            'result' => ['nullable', 'string', 'max:100'],
            'result_status' => ['nullable', 'string', 'max:10'],
            'batch_as_batch_id' => ['required', 'integer', 'exists:batch_as_batches,id'],
            'user_as_user_id' => ['required', 'integer', 'exists:user_as_users,id'],
        ];
    }
}
