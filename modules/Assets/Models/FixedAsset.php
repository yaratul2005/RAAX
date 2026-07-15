<?php

namespace Modules\Assets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FixedAsset extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'tenant_id', 'asset_tag', 'name', 'description', 'acquisition_date',
        'acquisition_cost_cents', 'salvage_value_cents', 'lifespan_months',
        'depreciation_method', 'depreciation_rate_basis_cents', 'status'
    ];

    protected function casts(): array
    {
        return [
            'acquisition_date' => 'date',
            'acquisition_cost_cents' => 'integer',
            'salvage_value_cents' => 'integer',
            'lifespan_months' => 'integer',
            'depreciation_rate_basis_cents' => 'integer',
        ];
    }

    public function depreciationLogs(): HasMany
    {
        return $this->hasMany(DepreciationLog::class);
    }
}
