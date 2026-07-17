<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankStatement extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'tenant_id', 'bank_name', 'account_number', 'statement_date',
        'opening_balance_cents', 'closing_balance_cents', 'status'
    ];

    protected function casts(): array
    {
        return [
            'statement_date' => 'date',
            'opening_balance_cents' => 'integer',
            'closing_balance_cents' => 'integer',
        ];
    }

    public function lines(): HasMany
    {
        return $this->hasMany(BankStatementLine::class);
    }
}
