<?php

namespace App\Models\API\GMO\V1\Kurapital\AccountCandidate;

use App\Models\API\GMO\V1\user;
use Illuminate\Database\Eloquent\Model;

class AccountCandidate extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_account_candidates';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
    protected $fillable = [
        'id',
        'url',
        'content',
        'name',
        'representative_name',
        'prefecture_id',
        'is_company',
        'field_ids',
        'account_candidate_source_id',
        'account_candidate_status_id',
        'address',
        'phone1',
        'phone2',
        'phone3',
        'fax1',
        'fax2',
        'fax3',
        'email1',
        'email2',
        'email3',
        'note',
        'ng_note',
        'proposed_share',
        'proposed_share_method_id',
        'proposed_share_date',
        'proposed_share_user_id',
        'sent_fax_dm_date',
        'sent_fax_dm_user_id',
        'sent_first_documents_date',
        'sent_first_documents_user_id',
        'sent_second_documents_date',
        'sent_second_documents_user_id',
        'last_called_date',
        'last_called_user_id',
        'created_user_id',
        'modified_user_id',
    ];

    public function AccountCandidate()
    {
        return $this->belongsTo(AccountCandidate::class);
    }

    public function AccountCandidatestatus()
    {
        return $this->hasOne(AccountCandidatestatus::class, 'id', 'account_candidate_status_id');
    }
    //LastAccountCandidateNote
    public function AccountCandidateNote()
    {
        return $this->hasMany(AccountCandidateNote::class, 'account_candidate_id', 'id')->orderBy('created', 'ASC');

    }
    public function AccountCandidateNoteOnly()
    {
        return $this->hasMany(AccountCandidateNote::class, 'account_candidate_id', 'id')->orderBy('created', 'DESC');

    }
    public function AccountCandidatesTask()
    {
        return $this->hasMany(AccountCandidatesTask::class, 'account_candidate_id', 'id');
    }

    public function getLastAccountCandidateNoteAttribute()
    {

        $lastAccountCandiateNote = AccountCandidateNote::where('account_candidate_id', $this->id)->orderBy('created', 'DESC')->first();
        return $lastAccountCandiateNote;
    }

    public function createdBy()
    {
        return $this->hasOne(user::class, 'id', 'created_user_id');
    }

    public function modifiedBy()
    {
        return $this->hasOne(user::class, 'id', 'modified_user_id');
    }
    public function lastCalledUserDetail()
    {
        return $this->hasOne(user::class, 'id', 'last_called_user_id');
    }

    public function sentFaxDmUserDetail()
    {
        return $this->hasOne(user::class, 'id', 'sent_fax_dm_user_id');
    }

    public function sentFirstDocumentsUserDetail()
    {
        return $this->hasOne(user::class, 'id', 'sent_first_documents_user_id');
    }

    public function sentSecondDocumentsUserDetail()
    {
        return $this->hasOne(user::class, 'id', 'sent_second_documents_user_id');
    }

    /*
    last_called_user_id
sent_fax_dm_user_id
sent_first_documents_user_id
sent_second_documents_user_id
task.user_id
    */
}
