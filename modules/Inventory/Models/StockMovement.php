<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockMovement extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['id', 'tenant_id', 'inventory_batch_id', 'type', 'qty', 'reason'];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
        ];
    }
}
