<?php

namespace App\Models\API\GMO\V1;

use Illuminate\Database\Eloquent\Model;

class OnSiteBillingType extends Model
{
    protected $table = 'app_on_site_billing_types';
    protected $fillable = [
        'id',
        'name',
        'created_at',
        'updated_at',
    ];
}
