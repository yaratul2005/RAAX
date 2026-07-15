<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mushak63Invoice extends Model
{
    use SoftDeletes;

    // Explicitly set table to match the prompt/existing DB schema
    protected $table = 'mushak_6_3_invoices';

    protected $keyType = 'string';
    public $incrementing = false;

    // Based on standard NBR Mushak 6.3 structure and prompt requests
    protected $fillable = [
        'id', 'tenant_id', 'sales_order_id', 'challan_number', 'issue_date',
        'buyer_name', 'buyer_bin', 'seller_name', 'seller_bin',
        'subtotal_cents', 'vat_cents', 'total_payable_cents', 'is_high_value_audit'
    ];

    protected function casts(): array
    {
        return [
            'subtotal_cents' => 'integer',
            'vat_cents' => 'integer',
            'total_payable_cents' => 'integer',
            'is_high_value_audit' => 'boolean',
        ];
    }
}
