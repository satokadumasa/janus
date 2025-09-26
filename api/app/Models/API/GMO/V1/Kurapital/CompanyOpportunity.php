<?php

namespace App\Models\API\GMO\V1\Kurapital;

use Illuminate\Database\Eloquent\Model;

use App\Models\API\GMO\V1\Kurapital\Company;
use App\Models\API\GMO\V1\Kurapital\Opportunity;

class CompanyOpportunity extends Model
{
    protected $connection = 'gmoCrm';
    protected $table = 'app_companies_opportunities';
    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
        // return $this->belongsTo(Company::class);
    }
    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class);
    }
}
