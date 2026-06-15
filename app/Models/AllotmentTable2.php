<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllotmentTable2 extends Model
{
    protected $table = 'allotment_table2';

    protected $primaryKey = 'sr_no';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'fathers_or_husband_name',
        'application_number',
        'plot',
        'Sector',
        'ward',
        'town',
        'district',
    ];
}
