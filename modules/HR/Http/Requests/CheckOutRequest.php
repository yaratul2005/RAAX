<?php

namespace Modules\HR\Http\Requests;

use App\Http\Requests\BaseRequest;

class CheckOutRequest extends BaseRequest
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
            'employee_id' => ['required', 'uuid', 'exists:employees,id'],
            'check_out_time' => ['required', 'date'],
        ];
    }
}
