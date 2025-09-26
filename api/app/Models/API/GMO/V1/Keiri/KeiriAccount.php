<?php

namespace App\Models\API\GMO\V1\Keiri;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class KeiriAccount extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_accounts';
    //

    /**
     * Undocumented function
     *
     * @return void
     */
    public function KeiriAccount()
    {
        return $this->belongsTo(KeiriAccount::class);
    }
    /**
     * Undocumented function
     *
     * @return void
     */
    public function KeiriBills()
    {
        return $this->hasMany(Bill::class, 'account_id', 'id');
    }
    public function accountBank()
    {
        return $this->hasOne(KeiriAccountBank::class, 'account_id', 'id');
    }
    /**
     * Undocumented function
     *
     * @return void
     */
    public function KeiriReceipt()
    {
        return $this->hasMany(KeiriReceipt::class, 'account_id', 'id');
    }
    /**
     * Undocumented function
     *
     * @param [type] $input
     * @return void
     */
    public function MonthlyBills($input)
    {
        $month = $input;
        $bills = Bill::select([DB::raw("DATE_FORMAT(billing_period_to, '%Y-%m') as billing_period_to, SUM(transfer_amount) as transfer_amount, SUM(receipt_amount) as receipt_amount")]);
        if ($month != 0) {
            $bills->where('billing_period_to', 'like', "%$month%"); //日付
        }
        $bills->where('account_id', $this->id);
        $bills->groupBy('account_id');
        $bills->get();
        $bdata = '';
        $billing_period_to = '';
        $transfer_amount = 0;
        $receipt_amount = 0;
        foreach ($bills as $bill) {
            $bill = (object) $bill;
            $billing_period_to = $bill->billing_period_to;
            $transfer_amount = $bill->transfer_amount;
            $receipt_amount = $bill->receipt_amount;
        }
        return [
            'billing_period_to' => $billing_period_to,
            'transfer_amount' => $transfer_amount,
            'receipt_amount' => $receipt_amount,
        ];
    }
    /**
     * Undocumented function
     *
     * @param [type] $input
     * @return void
     */
    public function MonthlyReceipts($input)
    {
        $month = $input;
        $receipts = KeiriReceipt::query();
        $receipts->whereNull('bill_detail_id');
        $receipts->where('account_id', $this->id);
        if ($month != 0) {
            $receipts->where(function ($receipts) use ($month) {
                $receipts->whereBetween('receipt_date', [$month . '-01 00:00:00', $month . '-31　00：00：00']);
                $receipts->orWhere('receipt_date', null);
            });
        }
        $receipts->get();
        $receipt_data = [];
        foreach ($receipts as $unsettle) {
            $subarray['id'] = $unsettle->id;
            $subarray['receipt_date'] = $unsettle->receipt_date;
            $data = $this->getRevenue($unsettle);
            $subarray['opp_receipt_date'] = $data['receipt_date'];
            $revenue =  $data['revenue'];
            $subarray['revenue'] = $revenue;
            $receipt_data[] = $subarray;
        }
        $revenue = 0;
        $f_receipt_date = '';
        if ($receipt_data) {
            foreach ($receipt_data as $rdata) {
                $rdata = (object) $rdata;
                $revenue += $rdata->revenue;
                if (empty($f_receipt_date)) {
                    if ($rdata->receipt_date) {
                        $f_receipt_date = $rdata->receipt_date;
                    } else {
                        $f_receipt_date = $rdata->opp_receipt_date;
                    }
                }
            }
        }
        return [
            'revenue' => $revenue,
            'receipt_date' => $f_receipt_date
        ];
    }

    /**
     * Undocumented function
     *
     * @param [type] $unsettle
     * @return void
     */
    public function getRevenue($unsettle)
    {
        $opportunity = KeiriOpportunity::find($unsettle->opportunity_id);

        $other_cost = ($opportunity) ? $opportunity['other_cost'] : 0;
        $material_cost =  ($opportunity) ? $opportunity['material_cost'] : 0;
        $share_method_id = $this->share_method_id;
        $share = $this->share;
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
            'revenue' => floor($revenue),
            'receipt_date' => Carbon::parse($receipt_date)->format('Y-m')
        ];
    }
}
