<?php

namespace Modules\Manufacturing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BOMItem extends Model
{
    use SoftDeletes;
    protected $table = 'bom_items';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['id', 'tenant_id', 'bill_of_materials_id', 'raw_item_sku', 'qty_required', 'wastage_allowance_percentage_cents'];

    protected function casts(): array
    {
        return [
            'qty_required' => 'integer',
            'wastage_allowance_percentage_cents' => 'integer',
        ];
    }

    public function billOfMaterials(): BelongsTo
    {
        return $this->belongsTo(BillOfMaterials::class);
    }
}
