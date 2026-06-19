<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhysicalPossessionDocument extends Model
{
    public const TYPE_POSSESSION_CERTIFICATE = 'possession_certificate';

    public const TYPE_ALLOTMENT_LETTER = 'allotment_letter';

    public const TYPE_IDENTITY_PROOF = 'identity_proof';

    public const TYPE_PAYMENT_STATUS = 'payment_status';

    public const TYPE_OTHER_SUPPORTING = 'other_supporting_document';

    /** @var array<string, string> */
    public const TYPES = [
        self::TYPE_POSSESSION_CERTIFICATE => 'Possession Certificate',
        self::TYPE_ALLOTMENT_LETTER => 'Allotment Letter',
        self::TYPE_IDENTITY_PROOF => 'Identity Proof',
        self::TYPE_PAYMENT_STATUS => 'Payment Status / No Due Verification',
        self::TYPE_OTHER_SUPPORTING => 'Any Other Prescribed Supporting Document',
    ];

    /** @var array<string, string> Legacy types for older applications */
    public const LEGACY_TYPES = [
        'filled_form' => 'Signed Possession Certificate Request Form',
        'registration_certificate' => 'Registration Certificate',
        'provisional_possession_letter' => 'Provisional Possession Letter',
    ];

    /** @var list<string> All documents required before final submit */
    public const REQUIRED_TYPES = [
        self::TYPE_POSSESSION_CERTIFICATE,
        self::TYPE_ALLOTMENT_LETTER,
        self::TYPE_IDENTITY_PROOF,
        self::TYPE_PAYMENT_STATUS,
        self::TYPE_OTHER_SUPPORTING,
    ];

    /** @var list<string> */
    public const OPTIONAL_TYPES = [];

    public const MAX_UPLOAD_KB = 500;

    public const REVIEW_PENDING = 'pending';

    public const REVIEW_ACCEPTED = 'accepted';

    public const REVIEW_RETURNED = 'returned';

    protected $fillable = [
        'user_id',
        'private_purchaser_id',
        'property_auction_id',
        'mmsay_application_no',
        'application_id',
        'asset_id',
        'document_type',
        'file_path',
        'original_name',
        'file_size',
        'mime_type',
        'is_verified',
        'verified_at',
        'review_status',
        'officer_remarks',
        'returned_at',
        'returned_by',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(PhysicalPossessionApplication::class, 'application_id');
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->document_type]
            ?? self::LEGACY_TYPES[$this->document_type]
            ?? ucfirst(str_replace('_', ' ', $this->document_type));
    }

    public function iconName(): string
    {
        return match ($this->document_type) {
            self::TYPE_POSSESSION_CERTIFICATE, 'filled_form' => 'verified',
            self::TYPE_ALLOTMENT_LETTER, 'registration_certificate' => 'home_work',
            self::TYPE_IDENTITY_PROOF => 'badge',
            self::TYPE_PAYMENT_STATUS => 'payments',
            self::TYPE_OTHER_SUPPORTING, 'provisional_possession_letter' => 'folder_open',
            default => 'description',
        };
    }

    public static function applyFormFields(): array
    {
        $fields = [];

        foreach (self::TYPES as $key => $label) {
            $fields[$key] = [
                'label' => $label,
                'required' => in_array($key, self::REQUIRED_TYPES, true),
            ];
        }

        return $fields;
    }

    public function formattedSize(): string
    {
        if (! $this->file_size) {
            return '—';
        }

        if ($this->file_size < 1024 * 1024) {
            return round($this->file_size / 1024).' KB';
        }

        return number_format($this->file_size / (1024 * 1024), 2).' MB';
    }
}
