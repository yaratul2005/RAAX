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
            'entry_date' => ['required_without:date', 'nullable', 'date'],
            'date' => ['required_without:entry_date', 'nullable', 'date'],
            'reference' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'currency_code' => ['nullable', 'string', 'size:3'],
            'lines' => ['required', 'array', 'min:2'],
        ];
    }
}
