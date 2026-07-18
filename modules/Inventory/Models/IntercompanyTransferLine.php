<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntercompanyTransferLine extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'tenant_id', 'intercompany_transfer_id', 'item_sku', 'qty', 'unit_transfer_cost_cents'
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'unit_transfer_cost_cents' => 'integer',
        ];
    }

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(IntercompanyTransfer::class, 'intercompany_transfer_id');
    }
}
