<?php

namespace App\Models\API\GMO\V1;

use Illuminate\Database\Eloquent\Model;
use App\Models\API\GMO\V1\Houjin\Task;

class opportunityTask extends Model
{
    //
    protected $connection ='gmoCrm';

    protected $table = 'app_opportunity_tasks';

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function Task()
    {
        return $this->hasOne(task::class, 'id', 'task_id');
    }
}
