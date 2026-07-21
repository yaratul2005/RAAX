<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxLedgerEntry extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'tenant_id', 'tax_jurisdiction_id', 'source_type', 'source_id',
        'tax_rate_rule_id', 'base_amount_cents', 'tax_amount_cents', 'posted_at'
    ];

    protected function casts(): array
    {
        return [
            'base_amount_cents' => 'integer',
            'tax_amount_cents' => 'integer',
            'posted_at' => 'datetime',
        ];
    }
}
