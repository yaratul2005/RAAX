<?php

namespace Modules\Finance\Http\Requests;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Validator;

class CreateInvoiceRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:AP,AR'],
            'invoice_number' => ['required', 'string', 'max:255'],
            'party_id' => ['required', 'uuid'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'amount_cents' => ['required', 'integer', 'min:1'],
            'currency_code' => ['string', 'size:3']
        ];
    }
}
