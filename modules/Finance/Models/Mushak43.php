<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mushak43 extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'declaration_date',
        'product_name',
        'hs_code',
        'currency_code',
    ];
}
