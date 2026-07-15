<?php

namespace Modules\Manufacturing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionWorkOrder extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['id', 'tenant_id', 'bill_of_materials_id', 'work_order_number', 'qty_to_produce', 'total_overhead_cost_cents', 'status'];

    protected function casts(): array
    {
        return [
            'qty_to_produce' => 'integer',
            'total_overhead_cost_cents' => 'integer',
        ];
    }

    public function billOfMaterials(): BelongsTo
    {
        return $this->belongsTo(BillOfMaterials::class);
    }
}
