<?php
namespace App\Models\API\GMO\V1\Kurapital;

use Illuminate\Database\Eloquent\Model;
use App\Models\API\GMO\V1\Kurapital\OnSiteBillingType;
use App\Models\API\GMO\V1\Kurapital\OnSiteBillingMethod;

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

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function onSiteBillingType()
    {
        return $this->belongsTo(OnSiteBillingType::class);
    }

    public function onSiteBillingMethod()
    {
        return $this->belongsTo(OnSiteBillingMethod::class);
    }
}
