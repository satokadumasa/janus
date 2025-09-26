<?php

namespace App\Models\API\GMO\V1;

use Illuminate\Database\Eloquent\Model;

class ReceiptPlan extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_receipt_plans';
    // public $timestamps = false;
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        "id",
        "amount",
        "kurapital_receipt_amount",
        "partner_receipt_amount",
        "receipt_date",
        "kurapital_bill_amount",
        "partner_bill_amount",
        "kurapital_deduction",
        "partner_deduction",
        "disclosure",
        "card_user_id",
        "approved_user_id",
        "payment_method_id",
        "receipt_plan_status_id",
        "opportunity_id",
        "account_id",
        "receipt_id",
        "note",
        "created",
        "modified",
        "report_id"
    ];
    public function receiptPlan(){
        return $this->belongsTo(ReceiptPlan::class);
    }
    public function opportunity()
    {
        return $this->hasOne(opportunity::class,'id', 'opportunity_id');
    }
}
