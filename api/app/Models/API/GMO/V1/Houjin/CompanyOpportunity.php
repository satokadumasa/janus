<?php

namespace App\Models\API\GMO\V1\Houjin;

// use App\Models\API\GMO\V1\Kurapital\EstamationOpportunity;
// use App\Models\API\GMO\V1\opportunity;

use App\Models\API\GMO\V1\Kurapital\Houjin\account;
use App\Models\API\GMO\V1\Kurapital\Houjin\InvoiceOpportunity;
use App\Models\API\GMO\V1\Kurapital\Houjin\PhotoReportOpportunity;
use Illuminate\Database\Eloquent\Model;

class CompanyOpportunity extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_companies_opportunities';
    public $timestamps = false;
    //
    protected $fillable = [
        'id',
        'action_required',
        'company_id',
        'opportunity_id',
        'incharge_name',
        'alternative_incharge_name',
        'incharge_phonenumber',
        'alternative_incharge_phonenumber',
        'payment_method',
        'preferred_date',
        'have_alternative_incharge',
        'receipt_date',
        'in_house'
    ];

    public function opportunties(){
        return $this->hasOne(opportunity::class, 'id', 'opportunity_id');
    }
    public function Estimations()
    {
        return $this->hasMany(Estamation::class, 'id' , 'estimation_id');
    }
    public function companyDetail(){
        return $this->hasOne(Houjin::class,'id','company_id');
    }
    public function estimationOpportunities(){
        return $this->hasMany(EstimationOpportunity::class,'opportunity_id','opportunity_id');
    }
    public function opportunty(){
        return $this->hasOne(opportunity::class, 'id', 'opportunity_id');
    }

    public function invoiceOpportunities(){
        return $this->hasMany(InvoiceOpportunity::class,'opportunity_id','opportunity_id');
    }

    public function photoReportOpportunities(){
        return $this->hasMany(PhotoReportOpportunity::class,'opportunity_id','opportunity_id');
    }

    public function accountOpportunities(){
        return $this->hasMany(account::class,'id','opportunity_id');
    }

}
