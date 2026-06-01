<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    protected $table = 'sectors';
    protected $primaryKey = 'SectorId';
    public $timestamps = false;
}
