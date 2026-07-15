<?php

namespace Modules\Assets\Http\Requests;

use App\Http\Requests\BaseRequest;

class CreateAssetRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asset_tag' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'acquisition_date' => ['required', 'date', 'before_or_equal:today'],
            'acquisition_cost_cents' => ['required', 'integer', 'min:1'],
            'salvage_value_cents' => ['required', 'integer', 'min:0', 'lt:acquisition_cost_cents'],
            'lifespan_months' => ['required', 'integer', 'min:1'],
            'depreciation_method' => ['required', 'in:straight_line,reducing_balance'],
            'depreciation_rate_basis_cents' => ['required_if:depreciation_method,reducing_balance', 'integer', 'min:1', 'max:10000'],
        ];
    }
}
