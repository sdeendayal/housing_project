<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsBanner extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
        'displaybaneer',
        'status'
    ];
}
