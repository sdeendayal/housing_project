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
        'secure_id',
    ];

    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->secure_id)) {
                $user->secure_id = md5(uniqid(microtime() . rand(), true));
            }
        });
    }

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

    public function hasRole(string $slug): bool
    {
        return $this->role === $slug;
    }

    public function roleSlug(): ?string
    {
        return $this->role;
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
            'district_officer', 'department' => route('pp.officer.dashboard'),
            'admin', 'director', 'departmental' => route('mmsay.dashboard'),
            'ews_department' => route('ews.department.dashboard'),
            'ews_user' => route('ews.dashboard'),
            'ews_developer' => route('ews.developer.dashboard'),
            'mmgay-dtp' => route('pp.dtp.dashboard'),
            default => route('home'),
        };
    }
}
