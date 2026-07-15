<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['id', 'tenant_id', 'name', 'bin', 'credit_limit_cents', 'outstanding_balance_cents', 'status'];

    protected function casts(): array
    {
        return [
            'credit_limit_cents' => 'integer',
            'outstanding_balance_cents' => 'integer',
        ];
    }
}
