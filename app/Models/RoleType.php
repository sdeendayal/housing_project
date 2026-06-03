<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'role_group_id',
    ];

    // Each role type belongs to one user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Each role type belongs to one role group
    public function roleGroup(): BelongsTo
    {
        return $this->belongsTo(RoleGroup::class);
    }
}
