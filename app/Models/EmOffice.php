<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmOffice extends Model
{
    protected $table = 'em_offices';
    protected $primaryKey = 'BranchId';
    public $timestamps = false;
}
