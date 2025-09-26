<?php

namespace App\Models\API\GMO\V1\Keiri;

use Illuminate\Database\Eloquent\Model;

class KeiriAccountBank extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_account_banks';
    public $timestamps = false;


    public function account()
    {
        return $this->hasOne(KeiriAccount::class, 'id', 'account_id');
    }
}
