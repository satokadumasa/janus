<?php

namespace App\Console\Commands\API\V1;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\API\V1\Bill;
use App\Models\API\V1\Partner;
use App\Models\API\V1\BillAdjust;
use App\Models\API\V1\BillDetail;
use App\Models\API\V1\Opportunity;
use Illuminate\Support\Facades\DB;

class CreateBillsEveryDaySchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:dailybills';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create bills and send task';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $parents = Partner::where('check_daily_payment', 1)->whereNull("parent_company_id")->pluck('id'); //親会社id
        $branches = Partner::whereIn('parent_company_id', $parents)->pluck('id'); //親に紐づく子会社id
        $accountIds = $parents->concat($branches);

        $from = Carbon::now()->subDay(1)->format("Y-m-d 00:00:00");
        $to = Carbon::now()->subDay(1)->format("Y-m-d 23:59:59");

        //出張確定日が昨日の案件をもつ、パートナーを取得する
        // $targetAccounts = Partner::whereIn("id", $accountIds)->whereHas("opportunities", function ($query) use ($from, $to) {
        //     $query->whereBetween('work_date', [$from, $to]);
        // })->get();
        $accountIds_str = implode(",",$accountIds->all());
        $targetAccounts = DB::select(DB::raw("SELECT * FROM `app_accounts` WHERE `id` IN ({$accountIds_str}) AND EXISTS(SELECT * FROM `app_opportunities` WHERE `app_accounts`.`id`=`app_opportunities`.`account_id` AND `app_opportunities`.`work_date` BETWEEN '{$from}' AND '{$to}')"));

        $targetAccountIds = [];
        foreach ($targetAccounts as $account) {
            $accountId = !empty($account->parent_company_id) ? $account->parent_company_id : $account->id;
            $targetAccountIds[] = $accountId;
        }

        $billing_period_to = Carbon::now()->subDay(1)->format("Y-m-d"); //今日の日付から一日前
        $due_date = Carbon::now()->addDay(1)->format("Y-m-d"); //今日の日付から一日後

        //test
        // 2529
        // $this->createBills(2529, "2020-07-29", "2020-07-29");

        foreach ($targetAccountIds as $accountId) {
            $this->createBills($accountId, $billing_period_to, $due_date);
        }
    }

    public function createBills($accountId, $billing_period_to, $due_date)
    {
        //親会社と子会社IDの対を取得
        $accountIdLists = $this->findChildCompanyIds($accountId);
        $billable = false;
        foreach ($accountIdLists as $parentId => $accountIds) {
            $billing_period_to = $billing_period_to . ' 23:59:59';
            $due_date = $due_date . ' 23:59:59';

            $opportunities = $this->findBillableOpportunities($accountIds, $billing_period_to);

            $billAdjusts = $this->findUnsettledByAccountId($parentId);

            if (!$opportunities->isEmpty() || !$billAdjusts->isEmpty()) {
                //清算可能な案件が１つでもあるか.
                $billable = false;
                foreach ($opportunities as $opportunity) {
                    if ($this->isBillable($opportunity)) {
                        $billable = true;
                        break;
                    }
                }
                //清算可能な調整額が1つでもあるか.
                if (!$billAdjusts->isEmpty()) {
                    $billable = true;
                }
                //なければ次のパートナーへ.
                if (!$billable) {
                    //ステータス更新督促メールを送り、次のパートナーへ？
                    continue;
                }

                //トランザクションで一連の処理をまとめる
                DB::beginTransaction();
                $bill = Bill::create([
                    'account_id' => $parentId,
                    'billing_period_to' => $billing_period_to,
                    'bill_step_id' => 1,
                    'due_date' => $due_date
                ]);
                DB::commit();

                $billId = $bill->id;
                foreach ($opportunities as $opportunity) {
                    $this->createBillDetail($opportunity, $billId);
                    $this->addInBill($opportunity, $billId);
                    $this->updateBillStatus($opportunity);
                }

                foreach ($billAdjusts as $billAdjust) {
                    $this->putBillIdInAdjust($billAdjust, $billId);
                }

                if (!$this->updateAmount($billId)) {
                    return false;
                }
            }
        }
    }

    public function updateAmount($billId)
    {
        $bill = Bill::with('BillDetail', 'BillAdjust', 'account')->find($billId);
        $billAmount = 0;

        foreach ($bill->BillDetail as $billDetail) {
            $billAmount += $billDetail->transfer_amount;
        }
        foreach ($bill->BillAdjust as $billAdjust) {
            $billAmount += $billAdjust->amount;
        }
        $billAmount += $bill->special_amount;

        $bill->bill_amount = $billAmount;
        $bill->transfer_fee = $billAmount <= 0 ? 0 : $bill->account->transfer_fee;
        $bill->transfer_amount = $billAmount - round($bill->transfer_fee * (1.0 - $bill->account->share));
        $bill->share = $bill->account->share;
        $bill->save();

        return true;
    }

    /**
     * 清算作成時に調整額をbillと紐づける
     *
     * @param [type] $billAdjust
     * @param [type] $billId
     * @return void
     */
    public function putBillIdInAdjust($billAdjust, $billId)
    {
        $billAdjust->bill_id = $billId;
        $billAdjust->bill_step_id = 2;
        $billAdjust->save();
    }


    /**
     * 清算ステータスを更新します..
     *
     * @param [type] $opportunity
     * @return boolean
     */
    public function updateBillStatus($opportunity)
    {
        $billStatusId = 1;
        //精算明細が作られており、全て精算済なら3
        //精算明細が作られており、全て精算済なら2
        //精算明細が作られてない、あるいは作られているが精算データがないなら1
        if ($opportunity->BillDetails->isEmpty()) {
            $billStatusId = 1;
        } else {
            $allBilled = true; //全て精算済か
            $noBilled = true; //精算データが作られていないか

            foreach ($opportunity->BillDetails as $billDetail) {

                if (!empty($billDetail->bill_id)) {
                    $noBilled = false; //1つでも精算データが作られていたら
                }
                if ($billDetail->bill_step_id != 3) {
                    $allBilled = false; //1つでも精算済でなければ
                }
            }

            if ($noBilled) {
                $billStatusId = 1;
            } else if ($allBilled) {
                $billStatusId = 3;
            } else {
                $billStatusId = 2;
            }
        }
        // if ($opportunity->bill_status_id != $billStatusId) {
        // $opportunity->bill_status_id = $billStatusId;
        $oppUpdate = Opportunity::findOrFail($opportunity->id);
        $oppUpdate->update([
            'bill_status_id' => $billStatusId,
        ]);
        $oppUpdate->save();
        // }
        return true;
    }

    /**
     * 清算に組み込みます.
     *
     * @param [type] $opportunity
     * @param [type] $billId
     * @return boolean
     */
    public function addInBill($opportunity, $billId)
    {
        foreach ($opportunity->BillDetails as $billDetail) {
            if (empty($billDetail->bill_id)) {
                $billDetail->bill_id = $billId;
                $billDetail->bill_step_id = 1;
                $billDetail->save();
            }
        }
        return true;
    }

    /**
     * ある案件の清算明細を作成します.
     * 案件の未精算領収情報をもとに清算明細を作成します.
     *
     * @param [type] $opportunity
     * @param [type] $billId
     * @return boolean
     */
    public function createBillDetail($opportunity, $billId)
    {
        $targetReceipts = [];
        //清算明細に登録されていない領収情報を対象.
        foreach ($opportunity->receipts as $receipt) {
            if (empty($receipt->bill_detail_id)) {
                $targetReceipts[] = $receipt;
            }
        }

        //全ての領収情報が清算済なら何もしない.
        if (empty($targetReceipts)) {
            return false;
        }

        //清算明細の作成.
        $kurapitalReceiptAmount = 0;
        $partnerReceiptAmount = 0;

        foreach ($targetReceipts as $receipt) {
            if ($receipt->by_kurapital == 1) {
                $kurapitalReceiptAmount += $receipt->amount;
            } else {
                $partnerReceiptAmount += $receipt->amount;
            }
        }

        $totalReceiptAmount = $kurapitalReceiptAmount + $partnerReceiptAmount;
        //自社部隊用、売上5:5の場合、部材が大きくなれば粗の4:6にする.
        if ($opportunity->partnerDettail->share_method_id == 3 && $opportunity->partnerDettail->share == 0.5) {

            if ($opportunity->sales * 0.2 < $opportunity->material_cost) {
                //売上2割より部材費が大きければ
                $opportunity->partnerDettail->share_method_id = 1;
                $opportunity->partnerDettail->share = 0.4;
            }
        }

        $partnerDeduction = h_calculateNormalDeduction($opportunity, $opportunity->partnerDettail);

        $kurapitalDeduction = $opportunity->kurapital_cost;
        $specialDeduction = 0;

        if ($opportunity->partnerDettail->share_method_id != 3) {

            $specialDeduction = 0;
        } else {

            $partnerProfit = round(($totalReceiptAmount - $partnerDeduction - $kurapitalDeduction) * $opportunity->partnerDettail->share) - $opportunity->material_cost;
            $kurapitalProfit = round(($totalReceiptAmount - $partnerDeduction - $kurapitalDeduction) * (1 - $opportunity->partnerDettail->share));
            if ($kurapitalProfit > $partnerProfit) {

                $specialDeduction = round(($kurapitalProfit - $partnerProfit) / 2);
            } else {

                $specialDeduction = 0;
            }
        }

        $partnerAmount = round(($totalReceiptAmount - $partnerDeduction - $kurapitalDeduction) * $opportunity->partnerDettail->share) + $partnerDeduction + $specialDeduction;

        DB::beginTransaction();
        $billDetail = BillDetail::create([
            'receipt_amount' => $totalReceiptAmount,
            'kurapital_receipt_amount' => $kurapitalReceiptAmount,
            'partner_receipt_amount' => $partnerReceiptAmount,
            'share' => $opportunity->partnerDettail->share,
            'share_method_id' => $opportunity->partnerDettail->share_method_id,
            'partner_amount' => $partnerAmount,
            'transfer_amount' => $partnerReceiptAmount - $partnerAmount,
            'partner_deduction' => $partnerDeduction,
            'kurapital_deduction' => $kurapitalDeduction,
            'special_deduction' => $specialDeduction,
            'bill_step_id' => 1,
            'bill_id' => $billId,
            'opportunity_id' => $opportunity->id,
        ]);
        DB::commit();

        $billDetailId = $billDetail->id;

        // 対象の領収情報の清算明細IDを登録
        foreach ($targetReceipts as $receipt) {
            $receipt->bill_detail_id = $billDetailId;
            $receipt->save();
        }
        return true;
    }

    /**
     * 清算可能な明細(未精算の領収や明細が1つでもあるか)をチェックします.
     *
     * @param [type] $opportunity
     * @return boolean
     */
    public function isBillable($opportunity)
    {
        $targetReceipts = [];
        foreach ($opportunity->receipts as $receipt) {
            if (empty($receipt->bill_detail_id)) {
                $targetReceipts[] = $receipt;
            }
        }

        if (!empty($targetReceipts)) {
            return true;
        }

        $hasUnbilledBillDetail = false;

        foreach ($opportunity->BillDetails as $billDetail) {
            if (empty($billDetail->bill_id)) {
                $hasUnbilledBillDetail = true;
                break;
            }
        }
        return $hasUnbilledBillDetail;
    }

    /**
     * 清算前のデータを、パートナーを指定して取得します.
     *
     * @param [type] $accountId
     * @return Object
     */
    public function findUnsettledByAccountId($accountId)
    {
        $billAdjust = BillAdjust::where('account_id', $accountId)
            ->where('bill_step_id', 1)
            ->get();

        return $billAdjust;
    }

    /**
     * 特定のパートナーの、清算可能な案件を返します。
     *
     * @param [type] $accountIds
     * @param [type] $billing_period_to
     * @return Object
     */
    public function findBillableOpportunities($accountIds, $billing_period_to)
    {
        $opportunities = Opportunity::whereIn('account_id', $accountIds)
            ->where('status_id', 5)
            ->where(function ($query) {
                $query->whereNull('bill_status_id')->orWhere('bill_status_id', '<>', 3);
            })
            ->where('worked_date', '<', $billing_period_to)
            ->with('receipts', 'BillDetails', 'partnerDettail')
            ->get();

        return $opportunities;
    }

    /**
     * 子会社のパートナーIDを取得します.
     */
    public function findChildCompanyIds($accountId = null)
    {
        $accountQuery = Partner::query();

        if (!empty($accountId)) {

            $accountQuery->where('id', $accountId)->orWhere('parent_company_id', $accountId);
        }

        $accounts = $accountQuery->get();
        $returnData = [];

        foreach ($accounts as $account) {
            $id = empty($account->parent_company_id) ? $account->id : $account->parent_company_id;

            if (!isset($returnData[$id])) {
                $returnData[$id] = [];
            }
            $returnData[$id][] = $account->id;
        }

        return $returnData;
    }
}
