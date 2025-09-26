<?php

namespace App\Models\API\GMO\V1\Houjin;

use App\Models\API\V1\CompanyPaymentSite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\API\GMO\V1\Houjin\CompanyBranch;
use App\Models\API\GMO\V1\Houjin\CompanyPersonnel;
class Houjin extends Authenticatable
{
    use HasApiTokens, Notifiable;
    protected $guard = 'houjin';
    protected $connection = 'gmoCrm';
    protected $table = 'app_companies';
    //
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function houjin(){
        return $this->belongsTo(Houjin::class);
    }
    public function companyOpportunities(){
        return $this->hasMany(CompanyOpportunity::class, 'company_id', 'id');
    }

    public function companyPaymentSites() {
        return $this->hasOne(CompanyPaymentSite::class, 'id', 'payment_site_id');
    }

    public function companyBranches(){
        return $this->hasMany(CompanyBranch::class, 'company_id', 'id');
    }

    public function companyPersonnels()
    {
        return $this->hasMany(CompanyPersonnel::class, 'company_id', 'id');
    }
}
