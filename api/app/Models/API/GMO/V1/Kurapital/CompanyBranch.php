<?php

namespace App\Models\API\GMO\V1\Kurapital;

use Illuminate\Database\Eloquent\Model;

use App\Models\API\GMO\V1\Kurapital\Company;

class CompanyBranch extends Model
{
    protected $connection = 'gmoCrm';
    protected $table = 'app_company_branches';
    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }
}
