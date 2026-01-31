<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShipmentStoreRequest extends BaseFormRequest
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
            'batch_id' => ['required', 'integer', 'exists:batches,id'],
            'origin' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'vehicle_type' => ['nullable', 'string', 'max:255'],
            'mode' => ['required', 'string', 'in:Road,Air,Sea,Rail'],
            'route_distance' => ['required', 'numeric', 'min:0'],
            'fuel_type' => ['nullable', 'string', 'max:255'],
            'temperature' => ['nullable', 'numeric'],
            'co2_estimate' => ['nullable', 'numeric'],
            'departure_time' => ['nullable', 'date'],
            'arrival_time' => ['nullable', 'date', 'after_or_equal:departure_time'],
        ];
    }
}
