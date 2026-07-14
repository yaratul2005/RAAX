<?php

namespace Modules\HR\Http\Requests;

use App\Http\Requests\BaseRequest;
use Modules\HR\Models\Employee;
use Illuminate\Validation\Validator;

class CreateSalaryProfileRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'uuid'],
            'basic_salary_cents' => ['required', 'integer', 'min:0'],
            'house_rent_cents' => ['required', 'integer', 'min:0'],
            'medical_allowance_cents' => ['required', 'integer', 'min:0'],
            'transport_allowance_cents' => ['required', 'integer', 'min:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $employeeId = $this->input('employee_id');
            if ($employeeId && !Employee::where('id', $employeeId)->exists()) {
                $validator->errors()->add('employee_id', 'The selected employee does not exist in your tenant.');
            }
        });
    }
}
