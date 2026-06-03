<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // One user has one role type mapping
    public function roleType(): HasOne
    {
        return $this->hasOne(RoleType::class);
    }

    // Check if user belongs to a role group by slug (citizen, departmental)
    public function belongsToRoleGroup(string $slug): bool
    {
        $this->loadMissing('roleType.roleGroup');

        return $this->roleType?->roleGroup?->slug === $slug;
    }

    // Get the role group slug for redirects and checks
    public function roleGroupSlug(): ?string
    {
        $this->loadMissing('roleType.roleGroup');

        return $this->roleType?->roleGroup?->slug;
    }
}
