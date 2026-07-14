<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryBatch extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'tenant_id', 'warehouse_bin_id', 'item_sku', 'purchase_order_id', 'original_qty', 'remaining_qty', 'unit_cost_cents', 'currency_code'
    ];

    protected function casts(): array
    {
        return [
            'original_qty' => 'integer',
            'remaining_qty' => 'integer',
            'unit_cost_cents' => 'integer',
        ];
    }
}
