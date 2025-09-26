<?php

namespace App\Models\API\GMO\V1;

use App\Models\API\GMO\V1\account;
use App\Models\API\GMO\V1\Keiri\AccountBank;
use Illuminate\Database\Eloquent\Model;
use App\Models\API\GMO\V1\Keiri\BillAdjust;
use App\Models\API\GMO\V1\Keiri\BillDetail;
use App\Models\API\GMO\V1\Keiri\BillNote;
use App\Models\API\GMO\V1\Houjin\Bank;

class bill extends Model
{
    //
  protected $connection = 'gmoCrm';
    protected $table = 'app_bills';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [];
    public function account()
    {
        return $this->hasOne(account::class, 'id', 'account_id');
    }
    public function BillDetail()
    {
        return $this->hasMany(BillDetail::class, 'bill_id', 'id');
    }
    public function BillAdjust()
    {
        return $this->hasMany(BillAdjust::class);
    }
    public function BillNote()
    {
        return $this->hasMany(BillNote::class)->orderBy('created', 'DESC');
    }
    public function Bank()
    {
        return $this->hasOne(Bank::class, 'id', 'bank_id');
    }
    public function AccountBank()
    {
        return $this->hasOne(AccountBank::class);
    }
}
