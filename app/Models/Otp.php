<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Otp extends Model
{
    public const PURPOSE_CITIZEN_LOGIN = 'login';

    public const PURPOSE_MMGAV_VILLAGER_LOGIN = 'mmgav_villager_login';

    public const PURPOSE_DEPARTMENT_LOGIN = 'department_login';

    public const PURPOSE_EWS_CITIZEN_LOGIN = 'ews_citizen_login';

    public const PURPOSE_POSSESSION_CERTIFICATE = 'verify_possession_certificate';

    public const PURPOSE_ALLOTMENT_LETTER = 'verify_allotment_letter';

    protected $fillable = [
        'mobile_number',
        'purpose',
        'user_id',
        'otp',
        'expires_at',
        'verified_at',
        'attempts',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isActive(): bool
    {
        return is_null($this->verified_at) && ! $this->isExpired();
    }
}
