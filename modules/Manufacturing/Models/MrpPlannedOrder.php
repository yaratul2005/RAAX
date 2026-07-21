<?php

namespace Modules\Manufacturing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MrpPlannedOrder extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'tenant_id', 'mrp_run_id', 'item_sku', 'gross_requirement_qty',
        'net_requirement_qty', 'safety_stock_threshold', 'lead_time_days',
        'order_recommendation_type', 'planned_order_date', 'planned_delivery_date', 'status'
    ];

    protected function casts(): array
    {
        return [
            'gross_requirement_qty' => 'integer',
            'net_requirement_qty' => 'integer',
            'safety_stock_threshold' => 'integer',
            'lead_time_days' => 'integer',
            'planned_order_date' => 'date',
            'planned_delivery_date' => 'date',
        ];
    }

    public function mrpRun(): BelongsTo
    {
        return $this->belongsTo(MrpRun::class);
    }
}
