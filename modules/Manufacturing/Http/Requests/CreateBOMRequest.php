<?php

namespace Modules\Manufacturing\Http\Requests;

use App\Http\Requests\BaseRequest;

class CreateBOMRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'finished_item_sku' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.raw_item_sku' => ['required', 'string', 'max:255'],
            'items.*.qty_required' => ['required', 'integer', 'min:1'],
            'items.*.wastage_allowance_percentage_cents' => ['integer', 'min:0'],
        ];
    }
}
