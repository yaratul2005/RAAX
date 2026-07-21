<?php

namespace Modules\Finance\Http\Requests;

use App\Http\Requests\BaseRequest;

class StoreTaxRuleRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tax_jurisdiction_id' => ['required', 'uuid'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:standard,reduced,zero_rated'],
            'rate_basis_points' => ['required', 'integer', 'min:0'],
            'effective_from' => ['required', 'date'],
        ];
    }
}
