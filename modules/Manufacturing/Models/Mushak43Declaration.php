<?php

namespace Modules\Manufacturing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mushak43Declaration extends Model
{
    use SoftDeletes;

    protected $table = 'mushak_4_3_declarations';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['id', 'tenant_id', 'bill_of_materials_id', 'declaration_number', 'declared_cost_base_cents', 'declared_overhead_cents', 'declared_profit_cents', 'declared_sale_price_cents', 'declared_at'];

    protected function casts(): array
    {
        return [
            'declared_cost_base_cents' => 'integer',
            'declared_overhead_cents' => 'integer',
            'declared_profit_cents' => 'integer',
            'declared_sale_price_cents' => 'integer',
            'declared_at' => 'date',
        ];
    }

    public function billOfMaterials(): BelongsTo
    {
        return $this->belongsTo(BillOfMaterials::class);
    }
}
