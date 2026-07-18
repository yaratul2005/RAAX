<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IntercompanyTransfer extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'tenant_id', 'destination_tenant_id', 'transfer_number', 'total_cost_cents',
        'shipped_at', 'received_at', 'status'
    ];

    protected function casts(): array
    {
        return [
            'total_cost_cents' => 'integer',
            'shipped_at' => 'datetime',
            'received_at' => 'datetime',
        ];
    }

    public function lines(): HasMany
    {
        return $this->hasMany(IntercompanyTransferLine::class);
    }
}
