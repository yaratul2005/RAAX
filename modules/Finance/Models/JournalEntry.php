<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $tenant_id
 * @property string $entry_date
 * @property string|null $reference
 * @property string|null $description
 * @property int $amount
 * @property string $currency_code
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, JournalEntryLine> $lines
 */
class JournalEntry extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * @return HasMany<JournalEntryLine, $this>
     */
    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    protected $fillable = [
        'id',
        'tenant_id',
        'entry_date',
        'date',
        'reference',
        'description',
        'amount',
        'total_debit_cents',
        'currency_code',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'entry_date' => 'date',
            'amount' => 'integer',
        ];
    }
}
