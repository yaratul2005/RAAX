<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class RoleUser extends Pivot
{
    protected $table = 'role_user';
    public $incrementing = false;

    protected $fillable = [
        'role_id',
        'user_id',
        'tenant_id',
    ];
}
