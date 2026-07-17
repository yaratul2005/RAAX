<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankStatementLine extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'tenant_id', 'bank_statement_id', 'transaction_date',
        'reference', 'amount_cents', 'is_reconciled'
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'amount_cents' => 'integer',
            'is_reconciled' => 'boolean',
        ];
    }

    public function statement(): BelongsTo
    {
        return $this->belongsTo(BankStatement::class, 'bank_statement_id');
    }
}
