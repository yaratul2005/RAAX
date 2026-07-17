<?php

namespace Modules\Finance\Http\Requests;

use App\Http\Requests\BaseRequest;

class UploadStatementRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bank_name' => ['required', 'string'],
            'mt940_content' => ['required', 'string'], // In reality, might be a file upload, but string is easier for JSON API testing
        ];
    }
}
