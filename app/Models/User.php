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
        'block_id',
        'block_name',
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
        // 1. Check database role routing (highly dynamic, does not require editing code)
        $role = $this->assignedRole();
        if ($role) {
            return $role->dashboardUrl();
        }

        // 2. Fallback matching using roleSlug()
        $roleSlug = $this->roleSlug();

        return match ($roleSlug) {
            'villager' => route('mmgav.villager.dashboard'),
            'citizen' => ($this->scheme === 'MMGAY') ? route('mmgav.villager.dashboard') : route('citizen.dashboard'),
            'district_ceo', 'dc' => route('district.dashboard'),
            'district_officer' => route('pp.officer.dashboard'),
            'admin', 'director', 'department', 'departmental' => route('department.dashboard'),
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
