<?php

namespace App\Models\API\GMO\V1\Houjin;

use Illuminate\Database\Eloquent\Model;

class EstimationOpportunity extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_estimations_opportunities';
    public $timestamps = false;

    public function estimation(){
        return $this->hasMany(Estamation::class, 'id','estimation_id');
    }
}
