<?php

namespace Modules\Finance\Http\Requests;

use App\Http\Requests\BaseRequest;

class CreateDepositRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'challan_number' => ['required', 'string', 'max:255'],
            'deposit_date' => ['required', 'date'],
            'bank_branch' => ['required', 'string', 'max:255'],
            'code_of_analysis' => ['required', 'string', 'max:255'],
            'amount_cents' => ['required', 'integer', 'min:1'],
        ];
    }
}
