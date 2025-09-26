<?php

namespace App\Models\API\GMO\V1\Houjin;

use App\Models\API\V1\OtherBillDetail;
use App\Models\API\GMO\V1\Houjin\Houjin;
use App\Models\API\GMO\V1\Houjin\CompanyPersonnel;
use Illuminate\Database\Eloquent\Model;

class CompanyBranch extends Model
{
    protected $table = 'app_company_branches';
    protected $fillable = [
        'id',
        'name',
        'phone',
        'fax',
        'email_address',
        'estimation_email_address',
        'photo_report_email_address',
        'company_id',
        'created_at',
        'updated_at',
    ];

    public function companyDetail()
    {
        return $this->hasOne(Houjin::class, 'id', 'company_id');
    }

    public function companyPersonnels()
    {
        return $this->hasMany(CompanyPersonnel::class, 'branch_id', 'id');
    }
}
