<?php

namespace App\Http\Requests;

use App\Models\Delivery;
use Illuminate\Foundation\Http\FormRequest;

class DeliveryStoreRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('create', Delivery::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'batch_id' => ['required', 'exists:batches,id'],
            'delivery_date' => ['required', 'date'],
            'delivery_notes' => ['nullable', 'string'],
            'delivery_person' => ['required', 'string', 'max:255'],
            'delivery_contact' => ['nullable', 'string', 'max:255'],
            'delivery_address' => ['required', 'string'],
            'delivery_status' => ['required', 'string', 'in:pending,in_transit,delivered,failed'],
            'signature_recipient_name' => ['nullable', 'string', 'max:255'],
            'signature_data' => ['nullable', 'string'],
            'delivery_confirmation' => ['boolean'],
            'temperature_check' => ['boolean'],
            'quality_check' => ['boolean'],
            'additional_notes' => ['nullable', 'string'],
            'delivery_photos' => ['nullable', 'array'],
            'delivery_photos.*' => ['image', 'max:5120'], // 5MB max per file
            'customer_rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'customer_comments' => ['nullable', 'string'],
            'customer_complaints' => ['nullable', 'string'],
            'feedback_photos' => ['nullable', 'array'],
            'feedback_photos.*' => ['image', 'max:5120'], // 5MB max per file
            'feedback_status' => ['nullable', 'string', 'in:pending,submitted,reviewed,resolved,dismissed'],
            'admin_notes' => ['nullable', 'string'],
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
            'batch_id' => 'batch',
            'delivery_date' => 'delivery date',
            'delivery_notes' => 'delivery notes',
            'delivery_person' => 'delivery person',
            'delivery_contact' => 'contact number',
            'delivery_address' => 'delivery address',
            'delivery_status' => 'delivery status',
            'signature_recipient_name' => 'recipient name',
            'signature_data' => 'signature',
            'delivery_photos' => 'delivery photos',
            'customer_rating' => 'rating',
            'customer_comments' => 'comments',
            'customer_complaints' => 'complaints',
            'feedback_photos' => 'feedback photos',
            'feedback_status' => 'feedback status',
            'admin_notes' => 'admin notes',
        ];
    }
}
