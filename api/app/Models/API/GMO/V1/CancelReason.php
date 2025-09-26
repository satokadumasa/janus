<?php

namespace App\Models\API\GMO\V1;

use Illuminate\Database\Eloquent\Model;

class CancelReason extends Model
{
    //
    protected $connection ='gmoCrm';
    protected $table ='app_cancel_reasons';

    protected $fillable=[
        'status_id'
    ];
    public function cancelreason(){
        return $this->belongsTo(cancelreason::class);
    }
}
