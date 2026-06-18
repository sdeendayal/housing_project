<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsNews extends Model
{
    protected $table = 'cms_news';

    protected $fillable = [
        'title',
        'description',
        'type',
        'image',
        'pdf',
        'link',
        'status'
    ];
}