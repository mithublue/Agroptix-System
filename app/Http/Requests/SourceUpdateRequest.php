<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SourceUpdateRequest extends FormRequest
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
            'type' => ['nullable', 'string', 'max:20'],
            'gps_lat' => ['nullable', 'string'],
            'gps_long' => ['nullable', 'string'],
            'production_method' => ['required', 'in:['Natural','],
            'area' => ['nullable', 'string'],
            'status' => ['required', 'string', 'max:50'],
            'owner_id' => ['nullable', 'string'],
            'user_as_owner_id' => ['required', 'integer', 'exists:user_as_owners,id'],
        ];
    }
}
