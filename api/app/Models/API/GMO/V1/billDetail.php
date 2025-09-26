<?php

namespace App\Models\API\GMO\V1;

use Illuminate\Database\Eloquent\Model;

class billDetail extends Model
{
    //
    protected $connection ='gmoCrm';
    protected $table ='app_bill_details';

    protected $fillable=[
        'status_id'
    ];
    public function bill(){
        return $this->belongsTo(bill::class);
    }
}
