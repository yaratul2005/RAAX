<?php

namespace Modules\Assets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepreciationLog extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'tenant_id', 'fixed_asset_id', 'period_month',
        'depreciation_amount_cents', 'accumulated_depreciation_cents', 'book_value_cents'
    ];

    protected function casts(): array
    {
        return [
            'depreciation_amount_cents' => 'integer',
            'accumulated_depreciation_cents' => 'integer',
            'book_value_cents' => 'integer',
        ];
    }

    public function fixedAsset(): BelongsTo
    {
        return $this->belongsTo(FixedAsset::class);
    }
}
