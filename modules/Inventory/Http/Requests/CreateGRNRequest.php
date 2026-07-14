<?php

namespace Modules\Inventory\Http\Requests;

use App\Http\Requests\BaseRequest;

class CreateGRNRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'purchase_order_id' => ['required', 'uuid'],
            'grn_number' => ['required', 'string', 'max:255'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.item_sku' => ['required', 'string'],
            'lines.*.qty' => ['required', 'integer', 'min:1'],
            'lines.*.warehouse_bin_id' => ['required', 'uuid'],
        ];
    }
}
