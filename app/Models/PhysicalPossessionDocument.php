<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhysicalPossessionDocument extends Model
{
    protected $fillable = [
        'application_id',
        'document_type',
        'file_path',
        'original_name',
        'file_size',
        'mime_type',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(PhysicalPossessionApplication::class, 'application_id');
    }

    public function typeLabel(): string
    {
        return match ($this->document_type) {
            'filled_form' => 'Signed Possession Certificate Request Form',
            'registration_certificate' => 'Registration Certificate',
            'provisional_possession_letter' => 'Provisional Possession Letter',
            default => ucfirst(str_replace('_', ' ', $this->document_type)),
        };
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
