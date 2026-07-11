<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalEntry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'entry_date',
        'reference',
        'description',
        'amount',
        'currency_code',
    ];
}
