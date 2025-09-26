<?php

namespace App\Models\API\GMO\V1\Kurapital\AccountCandidate;

use App\Models\API\GMO\V1\user;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_tasks';


    public function Task(){
        return $this->belongsTo(Task::class);
    }

    public function AccountCandidatesTask(){
        return $this->hasOne(AccountCandidatesTask::class, 'task_id', 'id');
    }

    public function userDetail(){
        return $this->hasOne(user::class, 'id', 'user_id');

    }
}
