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
        return auth()->user()->can('create', \App\Models\Source::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'type' => ['nullable', 'string', 'max:20'],
            'gps_lat' => ['required', 'string'],
            'gps_long' => ['nullable', 'string'],
            'production_method' => ['required', 'in:' . implode(',', array_keys(config('at.production_methods')))],
            'area' => ['nullable', 'string'],
        ];

        // If user has manage_source permission, make status and owner_id required
        if (auth()->user()->can('manage_source')) {
            $rules['status'] = ['required', 'string', 'in:' . implode(',', array_keys(config('at.source_status')))];
            $rules['owner_id'] = ['required', 'integer', 'exists:users,id'];
        } else {
            // For regular users, set default status and owner_id
            $this->merge([
                'status' => 'active',
                'owner_id' => auth()->user()->id
            ]);
        }

        return $rules;
    }
}
