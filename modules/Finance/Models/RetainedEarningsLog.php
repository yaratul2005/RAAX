<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RetainedEarningsLog extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'tenant_id', 'fiscal_year_id', 'closing_net_income_cents', 'retained_earnings_account_id', 'journal_entry_id'
    ];

    protected function casts(): array
    {
        return [
            'closing_net_income_cents' => 'integer',
        ];
    }

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function retainedEarningsAccount(): BelongsTo
    {
        return $this->belongsTo(LedgerAccount::class, 'retained_earnings_account_id');
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }
}
