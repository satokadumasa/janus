<?php

namespace App\Models\API\GMO\V1\Keiri;

use Illuminate\Database\Eloquent\Model;

class BankList extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_banks';
    public $timestamps = false;


    public function BankList(){
        return $this->belongsTo(BankList::class);
    }
}

