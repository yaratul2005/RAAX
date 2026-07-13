<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PermissionRole extends Pivot
{
    protected $table = 'permission_role';
    public $incrementing = false;

    protected $fillable = [
        'permission_id',
        'role_id',
        'tenant_id',
    ];
}
