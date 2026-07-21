<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $tenant_id
 * @property int $journal_entry_id
 * @property int $ledger_account_id
 * @property int $debit_amount
 * @property int $credit_amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read JournalEntry $journalEntry
 * @property-read LedgerAccount $ledgerAccount
 */
class JournalEntryLine extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'tenant_id',
        'journal_entry_id',
        'account_id',
        'ledger_account_id',
        'debit_cents',
        'credit_cents',
        'debit_amount',
        'credit_amount',
    ];

    /**
     * @return BelongsTo<JournalEntry, $this>
     */
    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    /**
     * @return BelongsTo<LedgerAccount, $this>
     */
    public function ledgerAccount(): BelongsTo
    {
        return $this->belongsTo(LedgerAccount::class);
    }
}
