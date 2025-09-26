<?php

namespace App\Models\API\GMO\V1\Houjin;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_banks';
    public $timestamps = false;

    protected $fillable = [];
}
