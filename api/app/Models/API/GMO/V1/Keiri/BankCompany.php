<?php

namespace App\Models\API\GMO\V1\Keiri;

use Illuminate\Database\Eloquent\Model;

class BankCompany extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_m_bank_companies';
    // public $timestamps = false;
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function BankCompany(){
        return $this->belongsTo(BankCompany::class);
    }
}
