<?php

namespace Modules\Finance\Http\Requests;

use App\Http\Requests\BaseRequest;

class StoreExchangeRateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from_currency' => ['required', 'string', 'size:3'],
            'to_currency' => ['string', 'size:3'], // Defaults to BDT in controller
            'rate_basis_points' => ['required', 'integer', 'min:1'],
            'effective_date' => ['required', 'date'],
        ];
    }
}
