<?php

namespace App\Models\API\GMO\V1;

use Illuminate\Database\Eloquent\Model;

class AccountPrefecture extends Model
{
    //
    protected $connection ='gmoCrm';
    protected $table ='app_accounts_prefectures';
    public $timestamps = false;
    protected $fillable =[
        'id',
        'account_id',
        'prefecture_id',
        'score',
    ];
    public function AccountPrefecture()
    {
        return $this->belongsTo(AccountPrefecture::class);
    }
}
