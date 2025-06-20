<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShipmentStoreRequest extends FormRequest
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
            'origin' => ['nullable', 'string'],
            'destination' => ['nullable', 'string'],
            'vehicle_type' => ['nullable', 'string'],
            'co2_estimate' => ['nullable', 'numeric', 'between:-999999.99,999999.99'],
            'departure_time' => ['nullable', 'string'],
            'arrival_time' => ['nullable', 'string'],
            'batch_as_batch_id' => ['required', 'integer', 'exists:batch_as_batches,id'],
        ];
    }
}
