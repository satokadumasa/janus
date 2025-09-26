<?php

namespace App\Models\API\GMO\V1;

use Illuminate\Database\Eloquent\Model;

class AccountCandidate extends Model
{
    //
    protected $connection ='gmoCrm';
    protected $table = 'app_account_candidates';

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

}
