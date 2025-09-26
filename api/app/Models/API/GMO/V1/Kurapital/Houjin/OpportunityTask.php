<?php

namespace App\Models\API\GMO\V1\Kurapital\Houjin;

use Illuminate\Database\Eloquent\Model;
use App\Models\API\GMO\V1\Kurapital\Task;

class OpportunityTask extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_opportunity_tasks';
    public $timestamps = false;

    public function OpportunityTask(){
        return $this->belongsTo(OpportunityTask::class);
    }

    public function opportunities(){
        return $this->hasMany(Opportunity::class,'id','opportunity_id');
    }

    public function task(){
        return $this->hasOne(Task::class,'id','task_id');
    }
}
