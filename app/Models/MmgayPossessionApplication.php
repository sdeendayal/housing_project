<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MmgayPossessionApplication extends Model
{
    protected $table = 'mmgay_possession_applications';

    protected static function booted(): void
    {
        static::creating(function (self $application) {
            if (empty($application->secure_id)) {
                $application->secure_id = self::generateSecureId();
            }
            if (empty($application->possession_id)) {
                do {
                    $num = rand(100000, 999999);
                } while (self::where('possession_id', $num)->exists());
                $application->possession_id = $num;
            }
        });
    }

    protected $fillable = [
        'user_id',
        'secure_id',
        'possession_id',
        'owner_id',
        'ppp_id',
        'member_id',
        'flat_id',
        'slip_id',
        'application_number',
        'district_id',
        'district_name',
        'block_id',
        'block_name',
        'mobile',
        'applicant_name',
        'father_name',
        'address',
        'status',
        'remarks',
        'citizen_visit_date',
        'visit_slot_1',
        'visit_slot_2',
        'visit_slot_3',
        'visit_instructions',
        'physical_possession_status',
        'possession_date',
        'meeting_slot',
        'plot_image',
        'latitude',
        'longitude',
        'image_capture_datetime',
        'possession_certificate',
        'site_engineer_file',
        'verified_by',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'citizen_visit_date' => 'datetime',
            'visit_slot_1' => 'datetime',
            'visit_slot_2' => 'datetime',
            'visit_slot_3' => 'datetime',
            'possession_date' => 'date',
            'image_capture_datetime' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id', 'DistrictId');
    }

    public function getRouteKeyName(): string
    {
        return 'secure_id';
    }

    public static function generateSecureId(): string
    {
        return md5(uniqid(rand(), true));
    }

    public static function isWhitelistedForPossession($registrationNo)
    {
        if (empty($registrationNo)) {
            return false;
        }

        // Environment-driven bypass for testing: Check local database instead of live API
        if (env('MMGAY_POSSESSION_BYPASS_API', app()->environment('local'))) {
            $owner = \Illuminate\Support\Facades\DB::table('ownermaster')
                ->where('RegistrationNo', $registrationNo)
                ->first();

            if ($owner) {
                return \Illuminate\Support\Facades\DB::table('registary')
                    ->where(function($q) use ($owner) {
                        $q->where(function($sub) use ($owner) {
                            if (!empty($owner->FlatId)) {
                                $sub->where('flatid', $owner->FlatId)
                                    ->whereNotNull('flatid')
                                    ->where('flatid', '!=', '');
                            } else {
                                $sub->whereRaw('0 = 1');
                            }
                        })
                        ->orWhere(function($sub) use ($owner) {
                            if (!empty($owner->RegistrationNo)) {
                                $sub->where('registrationNo', $owner->RegistrationNo)
                                    ->whereNotNull('registrationNo')
                                    ->where('registrationNo', '!=', '');
                            } else {
                                $sub->whereRaw('0 = 1');
                            }
                        });
                    })
                    ->exists();
            }

            return false;
        }

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)
                ->withHeaders([
                    'X-API-KEY' => 'HFA26@hry#',
                ])
                ->get('https://api.revenueharyana.gov.in/api/LandRegistration/getRegistrationforHFALand', [
                    'RegistrationNo' => $registrationNo,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return !empty($data['payload']);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("HFA Land Registration API Error for " . $registrationNo . ": " . $e->getMessage());
        }

        return false;
    }
}
