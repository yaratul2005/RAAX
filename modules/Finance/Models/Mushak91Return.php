<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mushak91Return extends Model
{
    use SoftDeletes;

    // Explicitly set table name since convention might guess mushak91_returns or similar
    protected $table = 'mushak_9_1_returns';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'tenant_id', 'tax_period', 'total_sales_value_cents', 'total_output_tax_cents',
        'total_purchases_value_cents', 'total_input_tax_cents', 'net_tax_payable_cents',
        'treasury_deposit_id', 'status'
    ];

    protected function casts(): array
    {
        return [
            'total_sales_value_cents' => 'integer',
            'total_output_tax_cents' => 'integer',
            'total_purchases_value_cents' => 'integer',
            'total_input_tax_cents' => 'integer',
            'net_tax_payable_cents' => 'integer',
        ];
    }

    public function treasuryDeposit(): BelongsTo
    {
        return $this->belongsTo(TreasuryDeposit::class);
    }
}
