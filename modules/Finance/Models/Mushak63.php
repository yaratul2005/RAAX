<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mushak63 extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'issue_date',
        'buyer_name',
        'buyer_bin',
        'total_value',
        'total_vat',
        'currency_code',
    ];
}
