<?php

namespace App\Models\API\GMO\V1\Kurapital;

use App\Models\API\GMO\V1\user;
use Illuminate\Database\Eloquent\Model;

class EstimationTask extends Model
{
    //
 protected $connection = 'gmoCrm';
    protected $table ='app_estimation_tasks';
    protected $fillable = [
        'estimation_id',
        'task_id',
    ];
    // public $timestamps = false;
    /**
     * Undocumented function
     *
     * @return void
     */
    public function task (){
        return $this->belongsTo(Task::class);
    }

    /**
     * estimation()
     *
     * @return void
     */
    public function estimation (){
        return $this->belongsTo(Estimation::class);
    }
}
