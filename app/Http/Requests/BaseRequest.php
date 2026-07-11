<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseRequest extends FormRequest
{
    protected function failedValidation(Validator $validator): void
    {
        $firstError = collect($validator->errors()->all())->first();
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'statusCode' => 422,
                'message' => $firstError,
                'data' => null,
            ], 422)
        );
    }
}
