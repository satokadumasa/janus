<?php

namespace App\Models\API\GMO\V1\Webhook;

use Illuminate\Database\Eloquent\Model;

class linePartner extends Model
{
    //
    protected $table = 'linepartners';
    protected $fillable = [
        'lineId',
        'partnerid',
        'partner_name',
    ];
}
