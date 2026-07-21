<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxRateRule extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'tenant_id', 'tax_jurisdiction_id', 'name', 'type', 'rate_basis_points', 'effective_from'
    ];

    protected function casts(): array
    {
        return [
            'rate_basis_points' => 'integer',
            'effective_from' => 'date',
        ];
    }
}
