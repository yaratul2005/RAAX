<?php

namespace Modules\EDI\Http\Requests;

use App\Http\Requests\BaseRequest;

class Inbound850Request extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payload' => ['required', 'array'],
            'payload.customer_bin' => ['nullable', 'string'],
            'payload.customer_name' => ['nullable', 'string'],
            'payload.po_number' => ['required', 'string'],
            'payload.items' => ['required', 'array', 'min:1'],
            'payload.items.*.sku' => ['required', 'string'],
            'payload.items.*.qty' => ['required', 'integer', 'min:1'],
            'payload.items.*.price_cents' => ['required', 'integer', 'min:0'],
        ];
    }
}
