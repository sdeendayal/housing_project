<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleGroup extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function roleTypes(): HasMany
    {
        return $this->hasMany(RoleType::class);
    }
}
