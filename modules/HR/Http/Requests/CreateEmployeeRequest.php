<?php

namespace Modules\HR\Http\Requests;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Validator;
use Modules\HR\Models\Department;
use Modules\HR\Models\Designation;

class CreateEmployeeRequest extends BaseRequest
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
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'regex:/^\+?[0-9]{10,15}$/'],
            'department_id' => ['required', 'uuid'],
            'designation_id' => ['required', 'uuid'],
            'joining_date' => ['required', 'date'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $deptId = $this->input('department_id');
            if ($deptId && ! Department::where('id', $deptId)->exists()) {
                $validator->errors()->add('department_id', 'The selected department does not exist in your tenant.');
            }

            $desigId = $this->input('designation_id');
            if ($desigId && ! Designation::where('id', $desigId)->exists()) {
                $validator->errors()->add('designation_id', 'The selected designation does not exist in your tenant.');
            }
        });
    }
}
