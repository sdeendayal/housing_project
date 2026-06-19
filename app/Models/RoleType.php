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
        'role_id',
        'Is_Active',
        'Is_Deleted',
        'role_group_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function roleGroup(): BelongsTo
    {
        return $this->belongsTo(RoleGroup::class);
    }
}
