<?php

namespace App\Models\API\GMO\V1\Kurapital;

use Illuminate\Database\Eloquent\Model;

class WorkTarget extends Model
{
    protected $connection = 'gmoCrm';
    protected $table = 'app_work_targets';

    /**
     * Undocumented function
     *
     * @return void
     */
    function WorkTarget(){
        return $this->belongsTo(WorkTarget::class);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function Opportunity(){
        return $this->hasMany(Opportunity::class,'work_target_id', 'id');
    }
}
