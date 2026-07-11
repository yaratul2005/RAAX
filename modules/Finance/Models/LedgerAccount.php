<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LedgerAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'account_code',
        'account_name',
        'account_type',
        'currency_code',
    ];
}
