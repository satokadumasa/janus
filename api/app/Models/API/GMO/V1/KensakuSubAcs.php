<?php

namespace App\Models\API\GMO\V1;

use Illuminate\Database\Eloquent\Model;

class KensakuSubAcs extends Model
{
    //
    protected $connection ='gmoCrm';
    protected $table = 'app_kensaku_subacs';
    public $timestamps = false;

    public function AccountCanditate()
    {
        return $this->hasMany(AccountCandidate::class, 'id', 'account_canditate_id');
    }

}
