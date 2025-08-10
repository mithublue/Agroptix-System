<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SourceUpdateRequest extends BaseFormRequest
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
            'production_method' => ['required', 'in:' . implode(',', array_keys(config('at.production_methods')))],
            'area' => ['nullable', 'string'],
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'country' => ['required', 'string', 'size:2'],
            'state' => ['required', 'string', 'max:100'],
        ];

        // If user has manage_source permission, make status and owner_id required
        if (auth()->user()->can('manage_source')) {
            $rules['status'] = ['required', 'string', 'in:' . implode(',', array_keys(config('at.source_status')))];
            $rules['owner_id'] = ['required', 'integer', 'exists:users,id'];
        } else {
            // For regular users, ensure they can't change the owner
            $this->merge([
                'status' => 'pending',
                'owner_id' => auth()->user()->id
            ]);
        }

        // Validate selected products (optional) ensuring they belong to the owner
        // Use the current request instance to ensure correct data when controller builds a FormRequest manually
        $ownerId = request()->input('owner_id') ?? optional(request()->route('source'))->owner_id ?? auth()->id();
        $rules['product_ids'] = ['nullable', 'array'];
        $rules['product_ids.*'] = [
            'integer',
            Rule::exists('product_user', 'product_id')->where(function ($q) use ($ownerId) {
                return $q->where('user_id', $ownerId);
            })
        ];

        return $rules;
    }
}
