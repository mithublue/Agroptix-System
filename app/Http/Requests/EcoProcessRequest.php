<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EcoProcessRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'batch_id' => 'required|exists:batches,id',
            'stage' => 'required|string|max:50',
            'data' => 'nullable|array',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'status' => 'required|in:pending,in_progress,completed,failed'
        ];
    }
}
