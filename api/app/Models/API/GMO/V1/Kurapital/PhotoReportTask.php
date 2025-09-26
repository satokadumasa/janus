<?php

namespace App\Models\API\GMO\V1\Kurapital;

use App\Models\API\GMO\V1\user;
use Illuminate\Database\Eloquent\Model;

class PhotoReportTask extends Model
{
    //
 protected $connection = 'gmoCrm';
    protected $table ='app_photo_report_tasks';
    protected $fillable = [
        'photo_report_id',
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
     * Undocumented function
     *
     * @return void
     */
    public function photo_report (){
        return $this->belongsTo(PhotoReport::class);
    }
}
