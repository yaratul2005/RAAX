<?php

namespace Modules\Sales\Http\Requests;

use App\Http\Requests\BaseRequest;

class CreateOrderRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'uuid'],
            'order_number' => ['required', 'string', 'max:255'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.item_sku' => ['required', 'string'],
            'lines.*.qty' => ['required', 'integer', 'min:1'],
            'lines.*.unit_price_cents' => ['required', 'integer', 'min:0'],
        ];
    }
}
