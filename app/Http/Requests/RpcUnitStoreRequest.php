<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RpcUnitStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Update this based on your authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'rpc_identifier' => [
                'required',
                'string',
                'max:255',
                Rule::unique('rpc_units', 'rpc_identifier')
                    ->ignore($this->route('rpcunit')),
            ],
            'capacity_kg' => 'required|numeric|min:0',
            'material_type' => 'required|string|in:plastic,metal,wood,other',
            'status' => 'required|string|in:available,in_use,damaged,in_repair,retired',
            'total_wash_cycles' => 'sometimes|integer|min:0',
            'total_reuse_count' => 'sometimes|integer|min:0',
            'initial_purchase_date' => 'sometimes|date',
            'last_washed_date' => 'nullable|date',
            'current_location' => 'nullable|string|max:255',
//            'notes' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'rpc_identifier.required' => 'The RPC identifier is required.',
            'rpc_identifier.unique' => 'This RPC identifier is already in use.',
            'capacity_kg.required' => 'The capacity is required.',
            'material_type.required' => 'Please select a material type.',
            'status.required' => 'Please select a status.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'rpc_identifier' => 'RPC identifier',
            'capacity_kg' => 'capacity',
            'material_type' => 'material type',
        ];
    }
}
