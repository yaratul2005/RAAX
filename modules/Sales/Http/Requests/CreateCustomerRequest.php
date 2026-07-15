<?php

namespace Modules\Sales\Http\Requests;

use App\Http\Requests\BaseRequest;

class CreateCustomerRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'bin' => ['nullable', 'string', 'regex:/^\d{9}$/'], // 9-digit BIN
            'credit_limit_cents' => ['required', 'integer', 'min:0'],
        ];
    }
}
