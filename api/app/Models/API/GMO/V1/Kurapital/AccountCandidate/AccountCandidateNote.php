<?php

namespace App\Models\API\GMO\V1\Kurapital\AccountCandidate;

use App\Models\API\GMO\V1\user;
use Illuminate\Database\Eloquent\Model;

class AccountCandidateNote extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_account_candidate_notes';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
    protected $fillable = [
        'user_id',
        'account_candidate_id',
        'note',
        'disabled',
    ];

    public function AccountCandidateNote()
    {
        return $this->belongsTo(AccountCandidateNote::class);
    }

    public function AccountCandidate()
    {
        return $this->hasOne(AccountCandidate::class, 'id', 'account_candidate_id');
    }
    public function userDetail()
    {
        return $this->hasOne(user::class, 'id', 'user_id');
    }
}
