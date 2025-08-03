<?php

namespace App\Http\Requests;

use App\Models\Delivery;
use Illuminate\Foundation\Http\FormRequest;

class DeliveryUpdateRequest extends BaseFormRequest
{
    /**
     * The delivery instance being updated.
     */
    protected $delivery;

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->delivery = $this->route('delivery');
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->delivery);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'batch_id' => ['sometimes', 'required', 'exists:batches,id'],
            'delivery_date' => ['sometimes', 'required', 'date'],
            'delivery_notes' => ['nullable', 'string'],
            'delivery_person' => ['sometimes', 'required', 'string', 'max:255'],
            'delivery_contact' => ['sometimes', 'required', 'string', 'max:255'],
            'delivery_address' => ['sometimes', 'required', 'string'],
            'delivery_status' => ['sometimes', 'required', 'string', 'in:pending,in_transit,delivered,failed'],
            'signature_recipient_name' => ['nullable', 'string', 'max:255'],
            'signature_data' => ['nullable', 'string'],
            'delivery_confirmation' => ['boolean'],
            'temperature_check' => ['boolean'],
            'quality_check' => ['boolean'],
            'additional_notes' => ['nullable', 'string'],
            'delivery_photos' => ['nullable', 'array'],
            'delivery_photos.*' => ['sometimes', 'image', 'max:5120'],
            'customer_rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'customer_comments' => ['nullable', 'string'],
            'customer_complaints' => ['nullable', 'string'],
            'feedback_photos' => ['nullable', 'array'],
            'feedback_photos.*' => ['sometimes', 'image', 'max:5120'],
            'feedback_status' => ['nullable', 'string', 'in:pending,submitted,reviewed,resolved'],
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
