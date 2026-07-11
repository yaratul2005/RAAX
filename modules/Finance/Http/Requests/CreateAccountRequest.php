<?php

namespace Modules\Finance\Http\Requests;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Validator;
use Modules\Finance\Models\ChartOfAccounts;

class CreateAccountRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'parent_id' => ['nullable', 'exists:chart_of_accounts,id'],
            'code' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:Asset,Liability,Equity,Revenue,Expense'],
            'is_reconcilable' => ['boolean'],
            'currency_code' => ['required', 'string', 'size:3'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $parentId = $this->input('parent_id');
            if ($parentId) {
                /** @var ChartOfAccounts|null $parent */
                $parent = ChartOfAccounts::find($parentId);
                if ($parent && $parent->type !== $this->input('type')) {
                    $validator->errors()->add('type', 'The account type must match the parent account type.');
                }
            }
        });
    }
}
