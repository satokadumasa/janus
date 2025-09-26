<?php
namespace App\Models\API\GMO\V1\Partner;

use Illuminate\Database\Eloquent\Model;
use App\Models\API\GMO\V1\Houjin\CompanyChecklist;
use App\Models\API\GMO\V1\Checklist;

class CompanyOpportunity extends Model
{
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

        public function checklists()
    {
        return $this->belongsToMany(Checklist::class, CompanyChecklist::class, 'company_id', 'checklist_id', 'company_id', 'id');
    }

    //relation
    public function Company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function Opportunity()
    {
        return $this->hasOne(Opportunity::class, 'id', 'opportunity_id');
    }
}
