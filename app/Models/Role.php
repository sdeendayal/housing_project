<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Route;

class Role extends Model
{
    protected $fillable = [
        'role_group_id',
        'name',
        'slug',
        'dashboard_route',
        'dashboard_path',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function roleGroup(): BelongsTo
    {
        return $this->belongsTo(RoleGroup::class);
    }

    public function roleTypes(): HasMany
    {
        return $this->hasMany(RoleType::class);
    }

    public function dashboardUrl(): string
    {
        if ($this->dashboard_route && Route::has($this->dashboard_route)) {
            return route($this->dashboard_route);
        }

        if ($this->dashboard_path) {
            return url($this->dashboard_path);
        }

        return route('home');
    }
}
