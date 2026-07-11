<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mushak61 extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'purchase_date',
        'supplier_name',
        'supplier_bin',
        'invoice_number',
        'total_amount',
        'vat_amount',
        'currency_code',
    ];
}
