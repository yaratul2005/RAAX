<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $tenant_id
 * @property string|null $parent_id
 * @property string $code
 * @property string $name
 * @property string $type
 * @property bool $is_reconcilable
 * @property string $currency_code
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read ChartOfAccounts|null $parent
 * @property-read Collection<int, ChartOfAccounts> $children
 */
class ChartOfAccounts extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'tenant_id',
        'parent_id',
        'code',
        'name',
        'type',
        'is_reconcilable',
        'currency_code',
    ];

    protected $casts = [
        'is_reconcilable' => 'boolean',
    ];

    /**
     * @return BelongsTo<ChartOfAccounts, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccounts::class, 'parent_id');
    }

    /**
     * @return HasMany<ChartOfAccounts, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(ChartOfAccounts::class, 'parent_id');
    }
}
