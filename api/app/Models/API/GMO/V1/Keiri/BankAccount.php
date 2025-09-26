<?php

namespace App\Models\API\GMO\V1\Keiri;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_m_bank_accounts';
    // public $timestamps = false;
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
    protected $fillable = [
        "id",
        "name",
        "bank_company_id",
        "store_name",
        "store_code",
        "account_kind",
        "account_number",
        "account_name",
        "account_short_name",
        "order",
        "is_disabled",
        "is_deleted",
        "created",
        "modified",
    ];
    public function Bankaccount()
    {
        $this->belongsTo(BankAccount::class);
    }
    public function bankAccountTransaction(){
        return $this->hasMany(BankAccountTransaction::class , 'm_bank_account_id', 'id');
    }


    public function getLastUpdatedAttribute(){
        $data = $this->id;
        $date = BankAccountTransaction::where('m_bank_account_id',$data)->orderBy('modified','desc')->first();
        return ($date) ? $date->modified :'';
    }

    public function BankCompany(){
        return $this->hasOne(BankCompany::class ,'id','bank_company_id');
    }
}
