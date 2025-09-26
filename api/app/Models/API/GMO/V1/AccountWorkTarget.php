<?php

namespace App\Models\API\GMO\V1;

use Illuminate\Database\Eloquent\Model;

class AccountWorkTarget extends Model
{
    //
    protected $connection ='gmoCrm';
    protected $table ='app_accounts_work_targets';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'account_id',
        'work_target_id',
        'note',
    ];
    public function WorkTarget()
    {
        return $this->hasOne(WorkTarget::class, 'id', 'work_target_id');
    }

}
