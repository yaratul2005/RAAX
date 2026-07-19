<?php

namespace Modules\Finance\Http\Requests;

use App\Http\Requests\BaseRequest;

class CreateVdsRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'finance_invoice_id' => ['required', 'uuid'],
            'withheld_amount_cents' => ['required', 'integer', 'min:1'],
            'deposit_date' => ['required', 'date'],
        ];
    }
}
