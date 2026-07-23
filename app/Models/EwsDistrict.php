<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EwsDistrict extends Model
{
    protected $table = 'ews_districts';

    protected $fillable = [
        'name',
    ];
}
