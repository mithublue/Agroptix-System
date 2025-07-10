<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SourceUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('edit_source', $this->route('source'));
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
            'production_method' => ['required', 'in:Natural,Organic,Mixed'],
            'area' => ['nullable', 'string'],
        ];

        // If user has manage_source permission, make status and owner_id required
        if (auth()->user()->can('manage_source')) {
            $rules['status'] = ['required', 'string', 'in:' . implode(',', array_keys(config('at.source_status')))];
            $rules['owner_id'] = ['required', 'integer', 'exists:users,id'];
        } else {
            // For regular users, ensure they can't change the owner
            $this->merge([
                'owner_id' => $this->route('source')->owner_id
            ]);
        }

        return $rules;
    }
}
