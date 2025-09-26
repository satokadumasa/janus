<?php

namespace App\Models\API\GMO\V1\Houjin;

use App\Models\API\V1\OtherBillDetail;
use App\Models\API\GMO\V1\Houjin\Houjin;
use App\Models\API\GMO\V1\Houjin\CompanyBranch;
use Illuminate\Database\Eloquent\Model;

class CompanyPersonnel extends Model
{
    protected $table = 'app_company_personnels';
    protected $fillable = [
        'id',
        'name',
        'phone',
        'fax',
        'email_address',
        'estimation_email_address',
        'photo_report_email_address',
        'company_id',
        'branch_id',
        'created_at',
        'updated_at',
    ];

    public function companyDetail()
    {
        return $this->hasOne(Houjin::class, 'id', 'company_id');
    }

    public function branchDetail()
    {
        return $this->hasOne(CompanyBranch::class, 'id', 'branch_id');
    }
}
