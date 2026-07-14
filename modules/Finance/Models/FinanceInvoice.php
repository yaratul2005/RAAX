<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceInvoice extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'tenant_id',
        'type',
        'invoice_number',
        'party_id',
        'issue_date',
        'due_date',
        'amount_cents',
        'paid_cents',
        'currency_code',
        'status',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
    ];

    // Add missing casts for integer fields
    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'due_date' => 'date',
            'amount_cents' => 'integer',
            'paid_cents' => 'integer',
        ];
    }

    /**
     * Get the outstanding balance in cents.
     */
    public function getOutstandingBalanceAttribute(): int
    {
        return max(0, $this->amount_cents - $this->paid_cents);
    }
}
