<?php

namespace App\Models\API\GMO\V1;

use Illuminate\Database\Eloquent\Model;

class opportunityservicePaper extends Model
{
    //
    protected $connection ='gmoCrm';
    protected $table = 'app_service_papers';
    // public $timestamps = false;
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
    protected $fillable = [
        'number',
        'account_id',
        'opportunity_id',
        'receipt_date'

    ];

}
