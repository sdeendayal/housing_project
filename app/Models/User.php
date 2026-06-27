<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'private_purchaser_id',
        'password',
        'role',
        'scheme',
        'Is_Active',
        'Is_Deleted',
        'district_id',
        'district_name',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function roleType(): HasOne
    {
        return $this->hasOne(RoleType::class);
    }

    public function assignedRole(): ?Role
    {
        $this->loadMissing('roleType.role');

        return $this->roleType?->role;
    }

    public function belongsToRoleGroup(string $slug): bool
    {
        $this->loadMissing('roleType.role.roleGroup', 'roleType.roleGroup');

        $groupSlug = $this->roleType?->role?->roleGroup?->slug
            ?? $this->roleType?->roleGroup?->slug;

        if (! $groupSlug) {
            return false;
        }

        return $groupSlug === $slug || $this->roleGroupAliases($slug)->contains($groupSlug);
    }

    public function hasRole(string $slug): bool
    {
        $this->loadMissing('roleType.role');

        if ($this->roleType?->role?->slug === $slug) {
            return true;
        }

        return $this->role === $slug;
    }

    public function roleGroupSlug(): ?string
    {
        $this->loadMissing('roleType.role.roleGroup', 'roleType.roleGroup');

        return $this->roleType?->role?->roleGroup?->slug
            ?? $this->roleType?->roleGroup?->slug;
    }

    public function roleSlug(): ?string
    {
        $this->loadMissing('roleType.role');

        return $this->roleType?->role?->slug ?? $this->role;
    }

    public function dashboardRoute(): string
    {
        $role = $this->assignedRole();

        if ($role) {
            return $role->dashboardUrl();
        }

        return match ($this->roleGroupSlug() ?? $this->role) {
            'citizen' => route('citizen.dashboard'),
            'department', 'departmental' => route('department.dashboard'),
            'district_officer' => route('pp.officer.dashboard'),
            default => route('home'),
        };
    }

    private function roleGroupAliases(string $slug): \Illuminate\Support\Collection
    {
        return match ($slug) {
            'department', 'departmental' => collect(['department', 'departmental']),
            default => collect([$slug]),
        };
    }
}
