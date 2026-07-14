<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseRequest extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'tenant_id', 'department_id', 'requested_by', 'total_estimated_cost_cents', 'currency_code', 'status'
    ];

    protected function casts(): array
    {
        return [
            'total_estimated_cost_cents' => 'integer',
        ];
    }
}
