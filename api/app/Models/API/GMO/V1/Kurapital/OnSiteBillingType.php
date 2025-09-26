<?php
namespace App\Models\API\GMO\V1\Kurapital;

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

    public function companyOnSiteBillingType() {
        return $this->hasOne(CompanyOnSiteBillingType::class, 'id', 'on_site_billing_type_id');
    }
}
