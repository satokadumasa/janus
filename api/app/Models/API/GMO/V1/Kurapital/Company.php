<?php

namespace App\Models\API\GMO\V1\Kurapital;

use Illuminate\Database\Eloquent\Model;
use App\Models\API\V1\CompanyPaymentSite;

class Company extends Model
{
    //
    protected $connection='gmoCrm';
    protected $table = 'app_companies';

    public function companyPaymentSites() {
        return $this->hasOne(CompanyPaymentSite::class, 'id', 'payment_site_id');
    }

    public function companyOnSiteBillingType() {
        return $this->hasOne(CompanyOnSiteBillingType::class, 'id', 'company_id');
    }
}
