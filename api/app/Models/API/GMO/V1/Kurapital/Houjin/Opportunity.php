<?php

namespace App\Models\API\GMO\V1\Kurapital\Houjin;

use Carbon\Carbon;
use App\Models\API\GMO\V1\Dial;
use App\Models\API\GMO\V1\user;
use App\Models\API\GMO\V1\Field;
use App\Models\API\GMO\V1\status;
use App\Models\API\GMO\V1\account;
use App\Models\API\GMO\V1\receipt;
use App\Models\API\GMO\V1\SubCategory;
use App\Models\API\GMO\V1\CancelReason;
use Illuminate\Database\Eloquent\Model;
use App\Models\API\GMO\V1\opportunityNote;
use App\Models\API\GMO\V1\opportunityTask;
use App\Models\API\GMO\V1\OpportunityWorkContent;
use App\Models\API\GMO\V1\opportunityservicePaper;

class Opportunity extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table ='app_opportunities';
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
        'is_double_window',
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
        'hash',
        'requested_lat_lng',
        'is_construction',
        'work_date_end',
        'is_photo_registered',
        'is_free_time',
        'is_attendance',
        'is_transfer',

        //    'dial_id'
    ];
    public function opportunity(){
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
    public function workContentDetail()
    {
        return $this->hasOne(OpportunityWorkContent::class, 'id','work_content_id');
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
    public function dial_details()
    {
        return $this->hasOne(Dial::class,'id', 'dial_id');
    }
    public function subcategory_detail()
    {
        return $this->hasOne(SubCategory::class,'id', 'sub_category_id');
    }
    public function fieldDetail()
    {
        return $this->hasOne(Field::class,'id', 'field_id');
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

    public function getDateFormatAttribute(){
        $date = '';
        // var_dump($this->work_date);exit;
        if($this->work_date){
            $before = Carbon::parse($this->work_date);
            $date = $before->format('Y/m/d H:i');
        }
        return $date;
    }
    public function getaAddressFormatAttribute(){
        // all_address
        $all_address = '';
        if(!empty($this->prefecture)){
            $all_address .= $this->prefecture;
        }
        if(!empty($this->city)){
            $all_address .= $this->city;
        }
        if(!empty($this->address)){
            $all_address .= $this->address;
        }
        if(empty($this->city && $this->prefecture && $this->address)){
            $all_address = '住所していない';
        }
        return $all_address;
    }
}