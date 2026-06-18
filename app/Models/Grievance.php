<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grievance extends Model
{
    public const STATUS_PENDING = 'Pending';

    public const STATUS_IN_PROGRESS = 'In Progress';

    public const STATUS_RESOLVED = 'Resolved';

    protected $fillable = [
        'secure_id',
        'application_id',
        'applicant_name',
        'mobile_number',
        'asset_id',
        'district_id',
        'district',
        'grievance_subject',
        'grievance_description',
        'grievance_status',
        'admin_remarks',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $grievance) {
            if (empty($grievance->secure_id)) {
                $grievance->secure_id = self::generateSecureId();
            }

            if (empty($grievance->grievance_status)) {
                $grievance->grievance_status = self::STATUS_PENDING;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'secure_id';
    }

    public static function generateSecureId(): string
    {
        do {
            $secureId = bin2hex(random_bytes(16));
        } while (self::where('secure_id', $secureId)->exists());

        return $secureId;
    }
}
