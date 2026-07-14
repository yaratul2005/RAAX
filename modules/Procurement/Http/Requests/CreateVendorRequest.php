<?php

namespace Modules\Procurement\Http\Requests;

use App\Http\Requests\BaseRequest;

class CreateVendorRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'tin' => ['nullable', 'string', 'regex:/^\d{12}$/'], // 12-digit TIN pattern
            'bin' => ['nullable', 'string', 'regex:/^\d{9}$/'],  // 9-digit BIN pattern
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string'],
        ];
    }
}
