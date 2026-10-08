<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EwsTown extends Model
{
    use HasFactory;

    protected $table = 'ews_towns';

    protected $fillable = [
        'district_id',
        'zone_id',
        'zone_name',
        'name',
        'type',
    ];

    public function zone()
    {
        return $this->belongsTo(EwsStpDistrict::class, 'zone_id');
    }

    public function district()
    {
        return $this->belongsTo(EwsDistrict::class, 'district_id');
    }

    public function flats()
    {
        return $this->hasMany(EwsBuilderFlat::class, 'town_id');
    }
}
