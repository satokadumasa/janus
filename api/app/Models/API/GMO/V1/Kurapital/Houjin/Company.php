<?php

namespace App\Models\API\GMO\V1\Kurapital\Houjin;

use App\Models\API\V1\CompanyPaymentSite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    //
    use SoftDeletes;
    protected $connection = 'gmoCrm';
    protected $table = 'app_companies';
    // public $timestamps = false;
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';
    const DELETED_AT = 'deleted';


    protected $fillable = [
        'id',
        'name',
        'representative_familyname',
        'representative_firstname',
        'representative_familyname_kana',
        'representative_firstname_kana',
        'representative_position',
        'postal_code',
        'prefecture',
        'city',
        'address',
        'billing_postal_code',
        'billing_prefecture',
        'billing_city',
        'billing_address',
        'phone1',
        'phone2',
        'phone3',
        'fax',
        'email1',
        'email2',
        'email3',
        'verification_email1',
        'verification_email2',
        'verification_email3',
        'url',
        'note',
        'email',
        'password',
        'created',
        'updated',
        'deleted',
        'payment_site_id',
        'payer_name',
        'report_mail_title',
        'report_mail_template',
        'estimate_mail_title',
        'estimate_mail_template',
        'workflow',
        'basic_research_fee',
        'budget_amount',
    ];
    public function companytiesOppotunities()
    {
        return $this->hasMany(CompanyOpportunity::class, 'company_id', 'id');
    }

    public function companyPaymentSites() {
        return $this->hasOne(CompanyPaymentSite::class, 'id', 'payment_site_id');
    }
}
