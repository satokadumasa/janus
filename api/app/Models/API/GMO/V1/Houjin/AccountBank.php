<?php

namespace App\Models\API\GMO\V1\Keiri;

use Illuminate\Database\Eloquent\Model;

class AccountBank extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_account_banks';
    public $timestamps = false;

    protected $fillable = [];
}
