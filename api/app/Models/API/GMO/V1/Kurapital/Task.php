<?php

namespace App\Models\API\GMO\V1\Kurapital;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_tasks';

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        'id',
        'content',
        'result',
        'limit_date',
        'completion_date',
        'importance',
        'is_disabled',
        'is_deleted',
        'task_status_id',
        'user_id',
        'create_user_id',
        'content_id',
    ];


    public function photoReportTask()
    {
        return $this->hasOne(PhotoReportTask::class, 'task_id');
    }
}
