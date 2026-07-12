<?php

namespace Modules\HR\Http\Requests;

use App\Http\Requests\BaseRequest;

class CreateShiftRequest extends BaseRequest
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
            'name' => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'date_format:H:i:s'],
            'end_time' => ['required', 'date_format:H:i:s'],
            'grace_period_minutes' => ['required', 'integer', 'min:0', 'max:120'],
        ];
    }
}
