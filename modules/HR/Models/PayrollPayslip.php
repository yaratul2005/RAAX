<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollPayslip extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'tenant_id',
        'employee_id',
        'billing_month',
        'gross_salary_cents',
        'unpaid_days',
        'late_deductions_cents',
        'withholding_tax_cents',
        'net_salary_cents',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'gross_salary_cents' => 'integer',
            'unpaid_days' => 'integer',
            'late_deductions_cents' => 'integer',
            'withholding_tax_cents' => 'integer',
            'net_salary_cents' => 'integer',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
