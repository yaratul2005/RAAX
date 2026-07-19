<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CurrencyExchangeRate extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'tenant_id', 'from_currency', 'to_currency', 'rate_basis_points', 'effective_date'
    ];

    protected function casts(): array
    {
        return [
            'rate_basis_points' => 'integer',
            'effective_date' => 'date',
        ];
    }
}
