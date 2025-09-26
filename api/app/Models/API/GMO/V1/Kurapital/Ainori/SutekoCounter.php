<?php

namespace App\Models\API\GMO\V1\Kurapital\Ainori;

use Illuminate\Database\Eloquent\Model;

class SutekoCounter extends Model
{
    //
  protected $connection = 'gmoCrm';
    protected $table = 'sutekocounter';
    public $timestamps = false;
}
