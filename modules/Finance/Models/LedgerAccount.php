<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $tenant_id
 * @property string $account_code
 * @property string $account_name
 * @property string $account_type
 * @property string $currency_code
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class LedgerAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'account_code',
        'account_name',
        'account_type',
        'currency_code',
    ];
}
