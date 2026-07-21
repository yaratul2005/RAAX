<?php

namespace Modules\Finance\Http\Requests;

use App\Http\Requests\BaseRequest;

class CreateBudgetLineRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'budget_id' => ['required', 'uuid'],
            'chart_of_accounts_id' => ['required', 'uuid'],
            'allocated_amount_cents' => ['required', 'integer', 'min:0'],
        ];
    }
}
