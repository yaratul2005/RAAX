<?php

namespace Modules\Inventory\Http\Requests;

use App\Http\Requests\BaseRequest;

class CreateTransferRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'destination_tenant_id' => ['required', 'uuid'],
            'transfer_number' => ['required', 'string', 'max:255'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.item_sku' => ['required', 'string'],
            'lines.*.qty' => ['required', 'integer', 'min:1'],
        ];
    }
}
