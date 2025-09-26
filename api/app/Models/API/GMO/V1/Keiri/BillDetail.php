<?php

namespace App\Models\API\GMO\V1\Keiri;

use App\Models\API\GMO\V1\bill;
use App\Models\API\GMO\V1\opportunity;
use App\Models\API\GMO\V1\receipt;
use Illuminate\Database\Eloquent\Model;

class BillDetail extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_bill_details';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        'bill_id',
        'bill_step_id'
    ];

    public function opportunity()
    {
        return $this->hasOne(opportunity::class, 'id', 'opportunity_id');
    }
    public function bill()
    {
        return $this->hasOne(bill::class, 'bill_id');
    }
    public function Receipts()
    {
        return $this->hasMany(receipt::class, 'bill_detail_id', 'bill_detail_id');
    }
}
