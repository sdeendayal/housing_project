<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyDrawDocument extends Model
{
    protected $table = 'property_draw_documents';

    protected $fillable = [
        'document_code',
        'scheme',
        'title',
        'district_id',
        'district_name',
        'location_label',
        'sector_label',
        'total_plots',
        'original_file_name',
        'file_path',
        'published_date',
        'sort_order',
        'IsActive',
        'IsDeleted',
        'CreatedDate',
        'CreatedBy',
        'ModifiedDate',
        'ModifiedBy',
    ];

    protected function casts(): array
    {
        return [
            'IsActive' => 'boolean',
            'IsDeleted' => 'boolean',
            'published_date' => 'datetime',
            'CreatedDate' => 'datetime',
            'ModifiedDate' => 'datetime',
        ];
    }
}
