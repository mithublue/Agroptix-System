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
        $rules = [
            'type' => ['nullable', 'string', 'max:20'],
            'gps_lat' => ['nullable', 'string'],
            'gps_long' => ['nullable', 'string'],
            'production_method' => ['required', 'in:Natural,Organic,Mixed'],
            'area' => ['nullable', 'string'],
        ];

        // Check if the authenticated user has the required permission
        if ($this->user()->can('manage_source')) {
            // If they do, add the validation rules for the admin fields
            $rules['status'] = ['required', 'string', 'in:pending,approved,rejected,active,inactive'];
            $rules['owner_id'] = ['required', 'integer', 'exists:users,id'];
        }

        return $rules;
    }
}
