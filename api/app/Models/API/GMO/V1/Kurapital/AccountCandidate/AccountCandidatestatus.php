<?php

namespace App\Models\API\GMO\V1\Kurapital\AccountCandidate;

use Illuminate\Database\Eloquent\Model;

class AccountCandidatestatus extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_account_candidate_statuses';

    public function AccountCandidatestatus(){
        return $this->belongsTo(AccountCandidatestatus::class);
    }
}
