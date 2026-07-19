<?php

namespace Modules\Finance\Http\Requests;

use App\Http\Requests\BaseRequest;

class CreateDebitNoteRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'purchase_order_id' => ['required', 'uuid'],
            'original_purchase_invoice_number' => ['required', 'string'],
            'returned_amount_cents' => ['required', 'integer', 'min:1'],
        ];
    }
}
