<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EwsDeveloperDistrict extends Model
{
    protected $table = 'ews_developer_districts';

    protected $fillable = [
        'name',
    ];
}
