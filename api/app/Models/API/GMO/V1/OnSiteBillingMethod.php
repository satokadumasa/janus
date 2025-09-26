<?php

namespace App\Models\API\GMO\V1;

use Illuminate\Database\Eloquent\Model;

class OnSiteBillingMethod extends Model
{
    protected $table = 'app_on_site_billing_methods';
    protected $fillable = [
        'id',
        'name',
        'note',
        'created_at',
        'updated_at',
    ];
}
