<?php

namespace App\Models\API\GMO\V1\Keiri;

use Carbon\Carbon;
use App\Models\API\GMO\V1\Houjin\Bank;
use Illuminate\Database\Eloquent\Model;
use App\Models\API\GMO\V1\Keiri\AccountBank;
use App\Models\API\GMO\V1\Kurapital\Opportunity;

class Bill extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_bills';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [

        'account_id',
        'billing_period_to',
        'bill_amount',
        'transfer_fee',
        'receipt_amount',
        'special_amount',
        'special_reason',
        'share',
        'transfer_amount',
        'bill_step_id',
        'bank_id',
        'account_bank_id',
        'bill_demand_status_id',
        'due_date',
        'receipt_date',
        'demand_count',
        'last_demand_date',
        'created',
        'modified',

    ];


    public function billmemo()
    {
        return $this->hasOne(BillMemo::class, 'id', 'id');
    }
    public function account()
    {
        return $this->hasOne(KeiriAccount::class, 'id', 'account_id');
    }
    public function BillDetail()
    {
        return $this->hasMany(BillDetail::class, 'bill_id', 'id');
    }
    public function BillAdjust()
    {
        return $this->hasMany(BillAdjust::class, 'bill_id', 'id');
    }
    public function BillNote()
    {
        return $this->hasMany(BillNote::class);
    }
    public function Bank()
    {
        return $this->hasOne(Bank::class, 'id', 'bank_id');
    }
    public function AccountBank()
    {
        return $this->hasOne(KeiriAccountBank::class, 'id', 'account_bank_id');
    }
    public function Expenses()
    {
        return $this->hasOne(Expense::class, 'bill_id', 'id');
    }

    public function opportunity()
    {

        return $this->hasMany(KeiriOpportunity::class, 'account_id', 'account_id');
    }
    public function status_opportunity()
    {
        return $this->opportunity()->whereIn('status_id', [1, 2, 3, 4]);
    }
    public function checkDailyPayment()
    {
        return $this->account()->where('check_daily_payment', 0);
    }

    public function totalReseipt()
    {
        // billing_period_to
    }

    public function MonthlyReceipts($input)
    {
        $receipts = KeiriReceipt::query()->where('id', 10);
        $receipts->get();
        $data = [];
        foreach ($receipts as $rec) {
            $data = 123;
        }

        return $data;
    }


    /**
     * Undocumented function
     *
     * @param [type] $unsettle
     * @return void
     */
    public function getRevenue($account, $unsettle)
    {
        $opportunity = KeiriOpportunity::find($unsettle->opportunity_id);

        $other_cost = ($opportunity) ? $opportunity['other_cost'] : 0;
        $material_cost =  ($opportunity) ? $opportunity['material_cost'] : 0;
        $share_method_id = $account->share_method_id;
        $share = $account->share;
        $receipt_date = $opportunity['receipt_date'];
        $amount = $unsettle->amount;
        $revenue = 0;
        if ($share_method_id == 1) {
            $revenue = ($amount - $material_cost - $other_cost) * (1.0 - $share);
        } else if ($share_method_id == 2) {
            $revenue = ($amount - $other_cost) * (1.0 - $share);
        } else {
            $kurapital = ($amount - $other_cost) * (1.0 - $share);
            $partner = ($amount - $other_cost) * $share - $material_cost;
            if ($kurapital <= $partner) {
                $revenue = ($amount - $other_cost) * (1.0 - $share);
            } else {

                $sales2 = $amount - $other_cost;
                $cost = ($sales2 + $material_cost - 2 * $share * $sales2) / (2 - 2 * $share);
                $revenue = ($amount - $cost - $other_cost) * (1.0 - $share);
            }
        }
        return [
            'revenue' => 100, //floor($revenue),
            'receipt_date' => Carbon::parse($receipt_date)->format('Y-m')
        ];
    }
    /**
     * Undocumented function
     *
     * @param [type] $id
     * @param [type] $period
     * @return void
     */
    public function getBillNoAttribute()
    {
        return preg_replace('/[^0-9]/', '', $this->billing_period_to) . "_" . $this->id;
    }

    /**
     * 未更新数
     */
    public function getUnsettleCountAttribute()
    {
        $count = count(Opportunity::where('account_id', $this->account_id)->whereIn('status_id', [3,4])->where('work_date', '<', Carbon::now())->get());

        if (!empty($count)) {
            return $count;
        } else {
            return "";
        }
    }

    /**
     * 清算調整額を修正.
     */
    public function modifySpecialAmount($specialData)
    {
        // return $specialData;
        // return $specialData;

        $bill = Bill::findOrFail($specialData['id']);
        return $this->BillAdjust;

        // $bill['Bill']['special_amount'] = $specialAmount;
        // $bill['Bill']['special_reason'] = $specialReason;
        // if(!$this->save($bill)) {
        //     return false;
        // }
        // return $this->updateAmount($bill);




        // $bill = $this->findById($bill['Bill']['id']);
        // $billAmount = 0;
        // foreach($bill['BillDetail'] as $billDetail) {
        //     $billAmount += $billDetail['transfer_amount'];
        // }
        // foreach($bill['BillAdjust'] as $billAdjust) {
        //     $billAmount += $billAdjust['amount'];
        // }
        // $billAmount += $bill['Bill']['special_amount'];
        // $bill['Bill']['bill_amount'] = $billAmount;
        // $bill['Bill']['transfer_fee'] = $billAmount <= 0 ? 0 : $bill['Account']['transfer_fee'];
        // $bill['Bill']['transfer_amount'] = $billAmount - round($bill['Bill']['transfer_fee'] * (1.0-$bill['Account']['share']));
        // $bill['Bill']['share'] = $bill['Account']['share'];
        // $this->save($bill);
        // return $bill = $this->findById($bill['Bill']['id']);
    }
}
