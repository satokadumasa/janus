<?php

namespace App\Models\API\GMO\V1\Kurapital\AccountCandidate;

use Illuminate\Database\Eloquent\Model;

class AccountCandidatesTask extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_account_candidate_tasks';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        'note',
        'account_candidate_id',
        'task_id',
    ];

    public function AccountCandidatesTask(){
        return $this->belongsTo(AccountCandidatesTask::class);
    }

    public function AccountCandidate(){
        return $this->hasOne(AccountCandidate::class,'id', 'account_candidate_id');
    }

    public function Task(){
        return $this->hasOne(Task::class, 'id', 'task_id');
    }
}
