<?php

namespace Modules\Finance\Http\Requests;

use App\Http\Requests\BaseRequest;

class StoreTaxJurisdictionRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'country_code' => ['required', 'string', 'size:2'],
            'currency_code' => ['required', 'string', 'size:3'],
        ];
    }
}
