<?php

namespace App\Models\API\GMO\V1\Keiri;

use App\Models\API\GMO\V1\account;
use App\Models\API\GMO\V1\bill;
use App\Models\API\GMO\V1\user;
use Illuminate\Database\Eloquent\Model;

class BillAdjust extends Model
{
    protected $connection = 'gmoCrm';
    protected $table = 'app_bill_adjusts';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        'amount',
        'bill_step_id',
        'bill_id',
        'user_id',
        'account_id',
        'note'
    ];

    public function Bill()
    {
        return $this->hasOne(bill::class);
    }
    public function User()
    {
        return $this->hasOne(user::class);
    }
    public function Account()
    {
        return $this->hasOne(account::class, 'id', 'account_id');
    }
}
