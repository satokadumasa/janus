<?php

namespace App\Models\API\GMO\V1\Keiri;

use App\Http\Resources\API\GMO\V1\Keiri\KeiriBillsResource;
use App\Http\Resources\API\GMO\V1\Keiri\KeiriReceiptResource;
use Illuminate\Database\Eloquent\Model;

class BankAccountTransaction extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_bank_account_transactions';
    // public $timestamps = false;
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        'id',
        'transaction_date',
        'transaction_content',
        'receipt_amount',
        'payment_amount',
        'balance',
        'memo',
        'm_bank_account_id',
        'm_bank_account_transaction_status_id',
        'note',
    ];

    public function BankAccountTransaction()
    {
        return $this->belongsTo(BankAccountTransaction::class);
    }
    public function bankaccount()
    {
        return $this->hasOne(BankAccount::class, 'id', 'm_bank_account_id');
    }

    public function amountMatchedReceipts($amount)
    {
        if (!empty($amount)) {
            $receipt_detail = KeiriReceipt::query()
                ->where('amount', $amount)
                ->where('received', 0)
                ->whereHas('opportunity', function ($receipt_detail) use ($amount) {
                    $receipt_detail->where('dial_id', '!=', 9);
                });

            return KeiriReceiptResource::collection(
                $receipt_detail
                ->with(
                   'opportunity',
                   'opportunity.status',
                   'opportunity.partnerDettail',
                   'opportunity.ownerDetail',
                   'opportunity.userDetail',
                   'opportunity.fieldDetail'
                )
                    ->paginate(100)
            );
        } else {
            return null;
        }
    }
    public function amountMatchedBills($amount)
    {
        if(!empty($amount)){
            $bills = Bill::where('transfer_amount',$amount)->where('bill_step_id','!=',3)->orderBy('id','ASC');
            return KeiriBillsResource::collection($bills->with('account')->paginate(100));
        }
    }
    // public function findOfsetBills($amount, $data){
    //     return $data;
    // }
}
