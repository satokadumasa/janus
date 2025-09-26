<?php

namespace App\Models\API\GMO\V1;

use Illuminate\Database\Eloquent\Model;

class WorkTarget extends Model
{
    //
    protected $connection ='gmoCrm';
    protected $table = 'app_work_targets';
    public $timestamps = false;

    public function workTarget(){
        return $this->belongsTo(WorkTarget::class);
    }
}
