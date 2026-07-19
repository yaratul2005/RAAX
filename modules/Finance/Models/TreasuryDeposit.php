<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TreasuryDeposit extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'tenant_id', 'challan_number', 'deposit_date', 'bank_branch',
        'code_of_analysis', 'amount_cents', 'status'
    ];

    protected function casts(): array
    {
        return [
            'deposit_date' => 'date',
            'amount_cents' => 'integer',
        ];
    }
}
