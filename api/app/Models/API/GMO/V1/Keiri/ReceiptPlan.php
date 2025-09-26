<?php

namespace App\Models\API\GMO\V1\Keiri;

use Illuminate\Database\Eloquent\Model;
use App\Models\API\GMO\V1\Keiri\InvoiceOpportunity;
use App\Models\API\GMO\V1\PaymentMethod;

class ReceiptPlan extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_receipt_plans';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
    protected $fillable = [
        'amount',
        'kurapital_receipt_amount',
        'partner_receipt_amount',
        'receipt_date',
        'kurapital_bill_amount',
        'partner_bill_amount',
        'kurapital_deduction',
        'partner_deduction',
        'disclosure',
        'card_user_id',
        'approved_user_id',
        'payment_method_id',
        'receipt_plan_status_id',
        'opportunity_id',
        'account_id',
        'receipt_id',
        'note',
        'created',
        'modified',
    ];
    public function ReceiptPlan()
    {
        $this->belongsTo(ReceiptPlan::class);
    }


    public function opportunity()
    {
        return $this->hasOne(KeiriOpportunity::class, 'id', 'opportunity_id');
    }

    public function invoiceOpportunity()
    {
        return $this->hasMany(InvoiceOpportunity::class, 'opportunity_id', 'opportunity_id');
    }

    public function partnerDetail()
    {
        return $this->hasOne(KeiriAccount::class, 'id', 'account_id');
    }

    public function PaymentMethod()
    {
        return $this->hasOne(PaymentMethod::class, 'id', 'payment_method_id');
    }

    public function UserDetails()
    {
        return $this->hasOne(User::class, 'id', 'card_user_id');
    }
}
