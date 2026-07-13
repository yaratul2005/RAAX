<?php

namespace Modules\HR\Http\Requests;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Validator;
use Modules\HR\Models\Employee;

class CheckInRequest extends BaseRequest
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
            'employee_id' => ['required', 'uuid'],
            'shift_id' => ['required', 'uuid'],
            'check_in_time' => ['required', 'date'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $employeeId = $this->input('employee_id');
            if ($employeeId && ! Employee::where('id', $employeeId)->exists()) {
                $validator->errors()->add('employee_id', 'The selected employee does not exist or is inactive in your tenant.');
            }
        });
    }
}
