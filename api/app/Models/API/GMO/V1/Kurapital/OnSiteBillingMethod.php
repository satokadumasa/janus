<?php

namespace App\Models\API\GMO\V1\Kurapital;

use Illuminate\Database\Eloquent\Model;
use App\Models\API\GMO\V1\Kurapital\CompanyOnSiteBillingType;

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

    public function companyOnSiteBillingType() {
        return $this->hasOne(CompanyOnSiteBillingType::class, 'id', 'on_site_billing_method_id');
    }
}
