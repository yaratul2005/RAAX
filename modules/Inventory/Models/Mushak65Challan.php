<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mushak65Challan extends Model
{
    use SoftDeletes;

    protected $table = 'mushak_6_5_challans';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'tenant_id', 'intercompany_transfer_id', 'challan_number', 'vehicle_number', 'driver_name', 'declared_at'
    ];

    protected function casts(): array
    {
        return [
            'declared_at' => 'datetime',
        ];
    }

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(IntercompanyTransfer::class, 'intercompany_transfer_id');
    }
}
