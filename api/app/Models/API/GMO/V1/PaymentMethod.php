<?php

namespace App\Models\API\GMO\V1;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_payment_methods';
}
