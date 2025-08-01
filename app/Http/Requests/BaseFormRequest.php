<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class BaseFormRequest extends FormRequest
{
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated();

        // Merge with all input data
        return array_merge($validated, $this->except(array_keys($validated)));
    }
}
