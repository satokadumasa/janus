<?php

namespace App\Models\API\GMO\V1;

use Carbon\Carbon;
use App\Models\API\V1\Partner;
use App\Models\API\GMO\V1\account;
use Illuminate\Database\Eloquent\Model;
use App\Models\API\GMO\V1\Keiri\BillDetail;
use App\Models\API\V1\OpportunityservicePaper;
use App\Models\API\GMO\V1\CompanyOpportunity;

use App\Models\API\GMO\V1\OnSiteBillingType;
use App\Models\API\GMO\V1\OnSiteBillingMethod;
use App\Models\API\GMO\V1\Kurapital\EstamationOpportunity;
use App\Models\API\GMO\V1\Kurapital\Estamation;

use App\Models\API\V1\OpportunityMaterials;

class opportunity extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_opportunities';
    // public $timestamps = false;
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        'hash',
        'material_cost',
        'firstname',
        'familyname',
        'firstname_kana',
        'familyname_kana',
        'gender_id',
        'generation_id',
        'company_name',
        'prefecture',
        'city',
        'address',
        'phone1',
        'phone2',
        'fax',
        'mail_address',
        'trouble',
        'note',
        'negotiation',
        'receipt_date',
        'work_date',
        'worked_date',
        'sales',
        'material_detail',
        'material_price',
        'highway_cost',
        'highway_from',
        'highway_to',
        'parking_cost',
        'stamp_cost',
        'allowed_cost',
        'allowed_detail',
        'other_cost',
        'kurapital_cost',
        'detail',
        'pay_by_invoice',
        'pay_by_service_paper',
        'payed_by_card',
        'field_id',
        'status_id',
        'account_id',
        'user_id',
        'owner_id',
        'is_additional_projects',
        'created',
        'modified',
        'incoming_phone',
        'cancel_reason_id',
        'work_content_id',
        'work_target_id',
        'proposal',
        'receipt_status_id',
        'bill_status_id',
        'opportunity_demand_status_id',
        'dial_id',
        'kaitori',
        'houzinflg',
        'seisanflg',
        //        status_detail,
        'negotiation',
        'cancel',
        'sendmail',
        'building_name',
        'shop_name',
        'sub_category_id',
        'shift_id',
        'building_room',
        //    'dial_id'
        'work_date_end',
        'order_amount',
        'order_date',
        'basic_research_fee',
        'budget_amount',
        'on_site_billing_type_id',
        'on_site_billing_method_id',
        'is_construction',
        'is_photo_registered',
        'is_free_time',
        'is_time_designation',
        'is_reschedule',
        'is_claim',
        'is_company',
        'is_attendance',
        'is_transfer',
        'is_emittion',
        'is_double_window',
        'is_irregular_billing',
        'irregular_billing',
        'zeroemi_discount',
        'kaiin_flyer_id',
    ];

    public function materials()
    {
        return $this->hasMany(OpportunityMaterials::class, 'opportunity_id')
			->orderBy('set_id', 'asc');
    }
    public function estamationOpportunities()
    {
        return $this->hasMany(EstamationOpportunity::class, 'opportunity_id');
    }
    // public function estamation()
    // {
    //     return $this->belongsTo(Estamation::class);
    // }

    public function adjoin()
    {
        return $this->hasOne(Adjoin::class, 'opportunity_id', 'id');
    }

    public function onSiteBillingTypeDetail()
    {
        return $this->hasOne(OnSiteBillingType::class, 'id', 'on_site_billing_type_id');
    }
    public function onSiteBillingMethodDetail()
    {
        return $this->hasOne(OnSiteBillingMethod::class, 'id', 'on_site_billing_method_id');
    }
    public function opportunity()
    {
        return $this->belongsTo(opportunity::class);
    }
    public function status()
    {
        return $this->hasOne(status::class, 'id', 'status_id');
    }
    public function receipts()
    {
        return $this->hasMany(receipt::class, 'opportunity_id', 'id');
    }
    public function partnerDettail()
    {
        return $this->hasOne(account::class, 'id', 'account_id');
    }
    public function accountDetail()
    {
        return $this->belongsTo(Partner::class, 'opportunity_id', 'id');
    }
    public function ownerDetail()
    {
        return $this->hasOne(user::class, 'id', 'owner_id');
    }
    public function userDetail()
    {
        return $this->hasOne(user::class, 'id', 'user_id');
    }
    public function cancelReason()
    {
        return $this->hasOne(CancelReason::class, 'id', 'cancel_reason_id');
    }
    public function oppTask()
    {
        return $this->hasMany(opportunityTask::class, 'opportunity_id');
    }
    public function workContentDetail()
    {
        return $this->hasOne(OpportunityWorkContent::class, 'id', 'work_content_id');
    }
    // public function KensakuOppo()
    // {
    //     return $this->hasMany(KensakuOppo::class,'opportunity_id', 'id');
    // }
    public function oppNotes()
    {
        return $this->hasMany(opportunityNote::class, 'opportunity_id');
    }
    public function serivePaper()
    {
        return $this->hasOne(opportunityservicePaper::class, 'opportunity_id');
    }
    /**
     * Undocumented function
     *
     * @return void
     */
    public function serivePapers()
    {
        return $this->hasMany(OpportunityservicePaper::class, 'opportunity_id');
    }
    public function dial_details()
    {
        return $this->hasOne(Dial::class, 'id', 'dial_id');
    }
    public function subcategory_detail()
    {
        return $this->hasOne(SubCategory::class, 'id', 'sub_category_id');
    }
    public function fieldDetail()
    {
        return $this->hasOne(Field::class, 'id', 'field_id');
    }

    public function BillDetails()
    {
        return $this->hasMany(BillDetail::class, 'opportunity_id', 'id');
    }
    public function companiesOpportunities()
    {
        return $this->hasOne(CompanyOpportunity::class, 'opportunity_id');
    }

    public function customerQuestionnaires()
    {
        return $this->hasOne(CustomerQuestionnaire::class, 'opportunity_id');
    }

    public function estimationMails()
    {
        return $this->hasMany(EstimationMail::class, 'opportunity_id');
    }

    public function estimationMailaddresses()
    {
        return $this->hasMany(EstimationMailaddress::class, 'opportunity_id');
    }

    // public function getWorkDateAttribute($value)
    // {
    // //    $date =  $this->worke_date;
    // //    $retunrDate = Carbon::parse($date)->format('Y-m-d h:i:s');
    //    return $value;
    // }
    // public function getworkTimeAttribute()
    // {
    //    $date =  $this->worke_date;
    //    $returnTime = Carbon::parse($date)->format('H:i');
    //    return $returnTime;
    // }

    public function getDateFormatAttribute()
    {
        $date = '';
        // var_dump($this->work_date);exit;
        if ($this->work_date) {
            $before = Carbon::parse($this->work_date);
            $date = $before->format('Y/m/d H:i');
        }
        return $date;
    }
    public function getDateEndFormatAttribute()
    {
        $date_end = '';
        // var_dump($this->work_date);exit;
        if ($this->work_date_end) {
            $before = Carbon::parse($this->work_date_end);
            $date_end = $before->format('H:i');
        }elseif($this->work_date){
			if($this->dial_id == 9 || $this->dial_id == 76 || $this->dial_id == 84){
				$before = Carbon::parse($this->work_date);
				$before->addHours(2); // 法人案件は2時間枠
				$date_end = $before->format('H:i');
			}else{
				$before = Carbon::parse($this->work_date);
				$before->addHours(4); // 一般案件は4時間枠
				$date_end = $before->format('H:i');
			}
		}
        return $date_end;
    }
    public function getaAddressFormatAttribute()
    {
        // all_address
        $all_address = '';
        if (!empty($this->prefecture)) {
            $all_address .= $this->prefecture;
        }
        if (!empty($this->city)) {
            $all_address .= $this->city;
        }
        if (!empty($this->address)) {
            $all_address .= $this->address;
        }
        if (empty($this->city && $this->prefecture && $this->address)) {
            $all_address = '住所していない';
        }
        return $all_address;
    }

    public function getIsQuestionnaireAnsweredAttribute()
    {
        if (!empty($this->customerQuestionnaires)) {
			return 1;
        }
        return 0;
    }

    public function getPhotosAttribute(){
        \Log::debug("Opportunity::getPhotosAttribute()");
        $photos = [];
        $crm_webroot = config('app.ptoho_report.report_ptoho_path');
        $report_ptoho_path_url = config('app.ptoho_report.report_ptoho_path_url');
        $searchPath = $crm_webroot . "/" . $this->id . "/*.{png,[jJ][pP][gG],[jJ][pP][eE][gG]}";

        if($this->is_photo_registered){
            $matchPaths = glob($searchPath, GLOB_BRACE);
            foreach($matchPaths as $path){
                // バイナリデータで渡すのやめました
                // $photo = base64_encode(file_get_contents($path));
                $url = str_replace($crm_webroot, $report_ptoho_path_url, $path);
                $data = [
                    'path' => $path,
                    'url' => $url,
                ];
                $photos[] = $data;
            }
        }
        return $photos;
    }
}
