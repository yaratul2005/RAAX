<?php

namespace Modules\Finance\Http\Requests;

use App\Http\Requests\BaseRequest;

class PostJournalRequest extends BaseRequest
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
            'entry_date' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'currency_code' => ['required', 'string', 'size:3'],
            'lines' => ['required', 'array', 'min:2'],
            'lines.*.ledger_account_id' => ['required', 'exists:ledger_accounts,id'],
            'lines.*.debit_amount' => ['required_without:lines.*.credit_amount', 'integer', 'min:0'],
            'lines.*.credit_amount' => ['required_without:lines.*.debit_amount', 'integer', 'min:0'],
        ];
    }
}
