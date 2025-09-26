<?php

namespace App\Models\API\GMO\V1;

use Illuminate\Database\Eloquent\Model;

use App\Models\API\V1\bill;

class account extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_accounts';

    public function bill() {
        return $this->hasMany(bill::class);
    }
}
