<?php

namespace App\Models\API\GMO\V1\Houjin;

use App\Models\API\GMO\V1\OnSiteBillingMethod;
use Illuminate\Database\Eloquent\Model;

class CompanyOnSiteBillingType extends Model
{
    protected $table = 'app_company_on_site_billing_types';
    protected $fillable = [
        'id',
        'company_id',
        'on_site_billing_type_id',
        'on_site_billing_method_id',
        'is_standard',
        'created_at',
        'updated_at',
    ];

    public function onSiteBillingMethodDetail()
    {
        return $this->hasOne(OnSiteBillingMethod::class, 'id', 'on_site_billing_method_id');
    }

}
