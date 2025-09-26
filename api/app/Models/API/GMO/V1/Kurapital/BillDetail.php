<?php

namespace App\Models\API\GMO\V1\Kurapital;

use Illuminate\Database\Eloquent\Model;

class BillDetail extends Model
{
    protected $connection = 'gmoCrm';
    protected $table = 'app_bill_details';

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
}
