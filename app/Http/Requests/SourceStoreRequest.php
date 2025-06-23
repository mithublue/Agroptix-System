<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SourceStoreRequest extends FormRequest
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
        $rules = [
            'type' => ['nullable', 'string', 'max:20'],
            'gps_lat' => ['nullable', 'string'],
            'gps_long' => ['nullable', 'string'],
            'production_method' => ['required', 'in:' . implode(',', array_keys(config('at.production_methods')))],
            'area' => ['nullable', 'string'],
        ];

        // Check if the authenticated user has the required permission
        if ($this->user()->can('create_source')) {
            // If they do, add the validation rules for the admin fields
            $rules['status'] = ['required', 'string', 'in:' . implode(',', array_keys(config('at.source_status')))];
            $rules['owner_id'] = ['required', 'integer', 'exists:users,id'];
        }

        return $rules;
    }
}
