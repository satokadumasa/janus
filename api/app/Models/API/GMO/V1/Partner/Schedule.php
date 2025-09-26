<?php

namespace App\Models\API\GMO\V1\Partner;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_partner_schedules';
    protected $fillable = [];


    public function Account(){
        return $this->hasOne(Account::class, 'id', 'partner_id');
    }
}
