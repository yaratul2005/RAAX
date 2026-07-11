<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mushak62 extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'sales_date',
        'customer_name',
        'customer_bin',
        'invoice_number',
        'total_amount',
        'vat_amount',
        'currency_code',
    ];
}
