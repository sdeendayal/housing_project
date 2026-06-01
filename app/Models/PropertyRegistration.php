<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyRegistration extends Model
{
    protected $table = 'property_registration';
    protected $primaryKey = 'AssetId';
    public $timestamps = false;
}
