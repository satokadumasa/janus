<?php

namespace App\Models\API\GMO\V1\Kurapital\Houjin;

use Illuminate\Database\Eloquent\Model;

class account extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_accounts';
    public $timestamps = false;

    public function accounts(){
        return $this->hasMany(account::class, 'id','opportunity_id');
    }
}
