<?php

namespace App\Models\API\GMO\V1\Kurapital\Houjin;

use App\Models\API\GMO\V1\Houjin\CompanyOpportunity;
use Illuminate\Database\Eloquent\Model;

class companies extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_companies';
    public $timestamps = false;

    public function companytiesOppotunities(){
        return $this->hasMany(CompanyOpportunity::class, 'company_id', 'id');
    }

}
