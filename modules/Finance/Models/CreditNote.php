<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditNote extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'tenant_id', 'sales_order_id', 'note_number', 'original_tax_invoice_number',
        'returned_amount_cents', 'adjusted_vat_cents', 'status'
    ];

    protected function casts(): array
    {
        return [
            'returned_amount_cents' => 'integer',
            'adjusted_vat_cents' => 'integer',
        ];
    }
}
