<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EncumbranceLedger extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'tenant_id', 'chart_of_accounts_id', 'source_type', 'source_id',
        'encumbered_amount_cents', 'relieved_amount_cents', 'status'
    ];

    protected function casts(): array
    {
        return [
            'encumbered_amount_cents' => 'integer',
            'relieved_amount_cents' => 'integer',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(LedgerAccount::class, 'chart_of_accounts_id');
    }
}
