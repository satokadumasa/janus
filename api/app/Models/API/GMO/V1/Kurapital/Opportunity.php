<?php

namespace App\Models\API\GMO\V1\Kurapital;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\API\GMO\V1\Dial;
use App\Models\API\GMO\V1\user;
use App\Models\API\GMO\V1\Field;
use App\Models\API\GMO\V1\status;
use App\Models\API\GMO\V1\subStatus;
use App\Models\API\GMO\V1\externalCollaborationStatus;
use App\Models\API\GMO\V1\account;
use App\Models\API\GMO\V1\EstimationMailaddress;
use App\Models\API\GMO\V1\EstimationMail;
use App\Models\API\GMO\V1\receipt;
use App\Models\API\GMO\V1\SubCategory;
use App\Models\API\GMO\V1\CancelReason;
use App\Models\API\GMO\V1\opportunityNote;
use App\Models\API\GMO\V1\opportunityTask;
use App\Models\API\GMO\V1\OpportunityWorkContent;
use App\Models\API\GMO\V1\opportunityservicePaper;
use App\Models\API\GMO\V1\Kurapital\ServicePaper;
use App\Models\API\GMO\V1\Kurapital\BillDetail;
use App\Models\API\V1\Adjoin;
use App\Models\API\GMO\V1\Kurapital\EstamationOpportunity;

use App\Models\API\GMO\V1\Houjin\CompanyChecklist;
use App\Models\API\GMO\V1\OnSiteBillingType;
use App\Models\API\GMO\V1\OnSiteBillingMethod;
use App\Models\API\V1\OpportunityMaterials;

class Opportunity extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_opportunities';
    // public $timestamps = false;
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
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
		'sub_status_id',
		'external_collaboration_status_id',
		'company_managed_building_id',
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
        'negotiation',
        'cancel',
        'sendmail',
        'building_name',
        'shop_name',
        'requested_lat_lng',
        'is_time_designation',
        'is_reschedule',
        'is_claim',
        'is_company',
        'work_date_end',
        'is_construction',
        'personnel_name',
        'estimation_email_address',
        'photo_report_email_address',
        'is_free_time',
        'on_site_billing_type_id',
        'on_site_billing_method_id',
        'basic_research_fee',
        'budget_amount',
        'is_attendance',
        'work_date_recived',
        'is_transfer',
        'is_emittion',
        'is_double_window',
        'is_irregular_billing',
        'irregular_billing',
        'zeroemi_discount',
        'kaiin_flyer_id',
    ];
    public function subStatus()
    {
        return $this->hasOne(subStatus::class, 'id', 'sub_status_id');
    }
    public function externalCollaborationStatus()
    {
        return $this->hasOne(externalCollaborationStatus::class, 'id', 'external_collaboration_status_id');
    }
    public function materials()
    {
        return $this->hasMany(OpportunityMaterials::class, 'opportunity_id')
			->orderBy('set_id', 'asc');
    }
    public function estamationOpportunities()
    {
        return $this->hasMany(EstamationOpportunity::class, 'opportunity_id');
    }

    public function opportunitiesPhotoReports()
    {
        return $this->hasMany(OpportunityPhotoReport::class, 'opportunity_id');
    }

    public function onSiteBillingType()
    {
        return $this->hasOne(OnSiteBillingType::class, 'id', 'on_site_billing_type_id');
    }
    public function onSiteBillingMethod()
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
        return $this->hasMany(receipt::class, 'opportunity_id');
    }

    public function invoiceOpportunities()
    {
        return $this->hasMany(InvoiceOpportunity::class, 'opportunity_id');
    }

    public function partnerDettail()
    {
        return $this->hasOne(account::class, 'id', 'account_id');
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

    /**
     * Undocumented function
     *
     * @return void
     */
    public function tasks()
    {
        return $this->hasManyThrough(Task::class, OpportunityTask::class, 'opportunity_id', 'id', 'id', 'task_id');
    }

    public function workContentDetail()
    {
        return $this->hasOne(OpportunityWorkContent::class, 'id', 'work_content_id');
    }

    public function workContent()
    {
        return $this->hasOne(WorkContent::class, 'id', 'work_content_id');
    }

    public function workTarget()
    {
        return $this->hasOne(WorkTarget::class, 'id', 'work_target_id');
    }

    public function companyOpportunity()
    {
        return $this->hasOne(CompanyOpportunity::class, 'opportunity_id');
    }

    public function ReceiptPlan()
    {
        return $this->hasOne(ReceiptPlan::class, 'opportunity_id', 'id');
    }
    public function oppNotes()
    {
        return $this->hasMany(opportunityNote::class, 'opportunity_id');
    }
    public function serivePaper()
    {
        return $this->hasMany(opportunityservicePaper::class, 'opportunity_id');
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

    public function servicePaperDetails()
    {
        return $this->hasOne(ServicePaper::class, 'opportunity_id', 'id');
    }

    public function billDetails()
    {
        return $this->hasMany(BillDetail::class, 'opportunity_id', 'id');
    }

    public function adjoin()
    {
        return $this->hasOne(Adjoin::class, 'opportunity_id', 'id');
    }

    public function photoReportTask()
    {
        return $this->hasOne(PhotoReportTask::class, 'opportunity_id');
    }

    public function workDates()
    {
        return $this->hasMany(WorkDate::class, 'opportunity_id');
    }

    public function estimationMails()
    {
        return $this->hasMany(EstimationMail::class, 'opportunity_id');
    }

    public function estimationMailaddresses()
    {
        return $this->hasMany(EstimationMailaddress::class, 'opportunity_id');
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

    public function getIsZippedPhotosAttribute(){
        $zip_keep_path = storage_path('report_photo_zip/'. $this->id);
        $check_path = $zip_keep_path . '/*.zip';
        if(!empty(glob($check_path))){
            return 1;
        }else{
            return 0;
        }
    }

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
    public function getReceiptPlanMethodAttribute()
    {
        // return $this->ReceiptPlan->id;
        $payment_method_id = null;
        if ($this->ReceiptPlan) {
            $payment_method_id = $this->ReceiptPlan->payment_method_id;
            if ($payment_method_id  != 0) {
                $cardMethod = CardMethod::where('id', $payment_method_id)->get();
                $user_id = $this->ReceiptPlan->card_user_id;
                $cardUser = user::where('id', $user_id)->get();
                if (count($cardUser) > 0) {

                    $returnValue = [
                        'cardDetail' => $cardMethod[0],
                        'user_detail' => $cardUser[0],
                    ];
                    return $returnValue;
                } else {
                    $returnValue = [
                        'cardDetail' => $cardMethod[0],
                        'user_detail' => null,
                    ];
                    return $returnValue;
                }
            } else {
                $returnValue = [
                    'cardDetail' => [
                        'id' => 0,
                        'name' => '何',
                        'order' => 0,
                        'disabled' => 0,
                    ],
                    'user_detail' => null,
                ];
                return $returnValue;
            }
        } else {
            return null;
        }
    }

    public function getIsReceivedAttribute()
    {
        if (!empty($this->bill_status_id)  && $this->bill_status_id != 1) {
            return true;
        }
        if (!empty($this->receipts)) {
            foreach ($this->receipts as $receipt) {
                if (!empty($receipt->bill_detail_id)) {
                    return true;
                }
            }
        }

        return false;
    }


    public function getIsFinishedAttribute()
    {
        if ($this->status_id == 5 || $this->status_id == 7 || $this->status_id == 8) {
            return true;
        }
        return false;
    }

    //for receipt_date
    public function getJptDateAttribute()
    {
        if (($this->receipt_date != '0000-00-00 00:00:00')) {
            return  Carbon::parse($this->receipt_date)->format('Y-m-d');
        } else {
            return  Carbon::now()->format('Y-m-d');
        }
    }
    public function getJptTimeAttribute()
    {
        if (($this->receipt_date != '0000-00-00 00:00:00')) {
            return Carbon::parse($this->receipt_date)->format('H:i');
        } else {
            return Carbon::now()->format('H:i');
        }
    }
    //for receipt_date end

    //for work_date
    public function getWorkDateOfDateAttribute()
    {
        if ((!empty($this->work_date) && $this->work_date != '0000-00-00 00:00:00' && $this->work_date != null)) {
            return  Carbon::parse($this->work_date)->format('Y-m-d');
        } else {
            return  Carbon::now()->format('Y-m-d');
        }
    }

    public function getWorkDateOfTimeAttribute()
    {
        if ((!empty($this->work_date) && $this->work_date != '0000-00-00 00:00:00' && $this->work_date != null)) {
            return Carbon::parse($this->work_date)->format('H:i');
        } else {
            return Carbon::now()->addHour()->format('H:00');
        }
    }
    //for work_date_end
    public function getWorkDateEndOfDateAttribute()
    {
        if ((!empty($this->work_date_end) && $this->work_date_end != '0000-00-00 00:00:00' && $this->work_date_end != null)) {
            return  Carbon::parse($this->work_date_end)->format('Y-m-d');
        } else {
            return  Carbon::now()->format('Y-m-d');
        }
    }

    public function getWorkDateEndOfTimeAttribute()
    {
        if ((!empty($this->work_date_end) && $this->work_date_end != '0000-00-00 00:00:00' && $this->work_date_end != null)) {
            return Carbon::parse($this->work_date_end)->format('H:i');
        } else {
			if($this->dial_id == 9 || $this->dial_id == 76 || $this->dial_id == 84){
            	return Carbon::now()->addHours(3)->format('H:00');
			}else{
            	return Carbon::now()->addHours(5)->format('H:00');
			}
        }
    }

    public function getOrderDateOfDateAttribute()
    {
        if (($this->order_date != '0000-00-00 00:00:00' && $this->order_date != null)) {
            return Carbon::parse($this->order_date)->format('Y-m-d');
        } else {
            return null;
        }
    }

    public function getHoujinInformationAttribute()
    {
        if ($this->dial_id == 9 || $this->dial_id == 76 || $this->dial_id == 84) {
            $companYopportunity = CompanyOpportunity::where('opportunity_id', $this->id)->get();
            if (count($companYopportunity) > 0) {
                $companYopportunity = $companYopportunity[0];
                $companYopportunity = (object) $companYopportunity;
                $houjinInfo = Company::where('id', $companYopportunity->company_id)->get();
                if (count($houjinInfo)) {
                    $houjinInfo = $houjinInfo[0];
                    $houjinInfo = (object) $houjinInfo;
                    $having_checklists = CompanyChecklist::where('company_id', $houjinInfo->id)->with([
                        'checklist' => function ($query){
                            $query->whereIn('checklist_group_id', [1,2]);
                        }
                    ])->get();
                    $checklists = [];
                    foreach($having_checklists as $list){
                        $checklists[] = $list->checklist->name;
                    }
                    // // 報告書作成不要と表示させたいがための処理
                    $returnValue = [
                        'id' => $houjinInfo->id,
                        'name' => $houjinInfo->name,
                        'checklists' => $checklists,
                        'deadline' => !empty($houjinInfo->companyPaymentSites) ? $houjinInfo->companyPaymentSites->deadline : null,
                        'payment_month_group_name' => !empty($houjinInfo->companyPaymentSites) ?  !empty($houjinInfo->companyPaymentSites->companyPaymentMonthGroups) ? $houjinInfo->companyPaymentSites->companyPaymentMonthGroups->name : null : null,
                        'payment_date' => !empty($houjinInfo->companyPaymentSites) ? $houjinInfo->companyPaymentSites->payment_date : null,
                        'other_payment_sites' => !empty($houjinInfo->companyPaymentSites) ? $houjinInfo->companyPaymentSites->other_payment_sites : null,
                    ];
                    return $returnValue;
                }

                return null;
            } else {
                return null;
            }
        }
    }
}