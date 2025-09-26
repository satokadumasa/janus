<?php

namespace App\Models\API\GMO\V1\Kurapital;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $connection = 'gmoCrm';
    protected $table = 'app_accounts';
}
