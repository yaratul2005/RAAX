<?php

namespace Modules\Manufacturing\Http\Requests;

use App\Http\Requests\BaseRequest;

class CreateWorkOrderRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bill_of_materials_id' => ['required', 'uuid'],
            'work_order_number' => ['required', 'string', 'max:255'],
            'qty_to_produce' => ['required', 'integer', 'min:1'],
            'total_overhead_cost_cents' => ['required', 'integer', 'min:0'],
        ];
    }
}
