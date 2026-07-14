<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSalary extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'tenant_id',
        'employee_id',
        'basic_salary_cents',
        'house_rent_cents',
        'medical_allowance_cents',
        'transport_allowance_cents',
    ];

    protected function casts(): array
    {
        return [
            'basic_salary_cents' => 'integer',
            'house_rent_cents' => 'integer',
            'medical_allowance_cents' => 'integer',
            'transport_allowance_cents' => 'integer',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getGrossSalaryCentsAttribute(): int
    {
        return $this->basic_salary_cents + $this->house_rent_cents + $this->medical_allowance_cents + $this->transport_allowance_cents;
    }
}
