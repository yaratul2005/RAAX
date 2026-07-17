<?php

namespace Modules\Finance\Http\Requests;

use App\Http\Requests\BaseRequest;

class CloseYearRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'retained_earnings_account_id' => ['required', 'uuid'],
        ];
    }
}
