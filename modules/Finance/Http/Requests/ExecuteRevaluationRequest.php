<?php

namespace Modules\Finance\Http\Requests;

use App\Http\Requests\BaseRequest;

class ExecuteRevaluationRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'target_month' => ['required', 'date_format:Y-m'],
            'target_currency' => ['required', 'string', 'size:3', 'not_in:BDT,bdt'],
        ];
    }
}
