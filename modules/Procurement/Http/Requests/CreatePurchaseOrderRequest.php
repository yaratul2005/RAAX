<?php

namespace Modules\Procurement\Http\Requests;

use App\Http\Requests\BaseRequest;

class CreatePurchaseOrderRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vendor_id' => ['required', 'uuid'],
            'po_number' => ['required', 'string', 'max:255'],
            'currency_code' => ['string', 'size:3'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.item_sku' => ['required', 'string'],
            'lines.*.qty' => ['required', 'integer', 'min:1'],
            'lines.*.unit_price_cents' => ['required', 'integer', 'min:0'],
        ];
    }
}
