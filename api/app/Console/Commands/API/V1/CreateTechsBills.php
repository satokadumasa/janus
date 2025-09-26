<?php

namespace App\Console\Commands\API\V1;

use App\Models\API\V1\Bill;
use App\Models\API\V1\BillAdjust;
use App\Models\API\V1\BillDetail;
use App\Models\API\V1\Opportunity;
use App\Models\API\V1\Partner;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateTechsBills extends Command
{
    /**
     * The name and signature of the consolee command.
     *
     * @var string
     */
    protected $signature = 'create:techsBills';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'techs liquidation data';

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
        $dayAgo = Carbon::now()->subDay(1)->format("Y-m-d");

        //工事部と前日の案件を取得する
        $techs = Partner::where('is_ars_employee', 1)
            ->where('disabled', 0)
            ->with(['opportunities' => function ($query) use ($dayAgo) {
                $query->where('work_date', 'like', "$dayAgo%");
            }])
            ->get();

        $skips = [];
        foreach ($techs as $tech) {
            if(in_array($tech->id, $skips)) {
                continue;
            }
            $skips[] = $tech->id;
            //前日の案件があるパートナーへアクションを起こす
            if ($tech->opportunities->isNotEmpty()) {
                sleep(2);   //  10秒1リクエスト回避処置
                $hasUnreport = false;

                //未更新の案件があるかチェックする
                foreach ($tech->opportunities as $oppo) {
                    if ($oppo->status_id === 4) {
                        $hasUnreport = true;
                    }
                }

                if ($hasUnreport) {
                    //未更新案件があれば、周知.
                    $this->notifyUnUpdatedStatus($tech);
                } else {
                    //問題なければ
                    $bill = $this->createBillForTechs($tech->id);

                    if(!empty($bill)) {
                        $billDetails = BillDetail::where('bill_id', $bill->id)->with('opportunity')->get();
                        //タスクを送る
                        $this->sendTransferTask($tech, $bill, $billDetails);
                    }
                }


            }
        }
    }

    /**
     * 振込を知らせるタスクを送る
     *
     * @param [type] $tech
     * @param [type] $bill
     * @param [type] $billDetails
     * @return void
     */
    public function sendTransferTask($tech, $bill, $billDetails)
    {
        $bill = $bill->load('BillAdjust');
        h_TransferTaskForTechsChatwork($tech, $bill, $billDetails);
    }

    /**
     * ステータス未更新の知らせをタスクで送る
     *
     * @param [type] $tech
     * @return void
     */
    public function notifyUnUpdatedStatus($tech)
    {
        //未更新の案件がある.
        h_UnUpdatedStatusForTechsChatwork($tech);
    }

    /**
     * 技術部向けに、請求情報を作成します.
     */
    public function createBillForTechs($techId)
    {
        $billingPeriodTo = Carbon::now()->format("Y-m-d 23:59:59");
        $dueDate = Carbon::now()->format("Y-m-d 12:00:00");

        $techIdLists = $this->findChildCompanyIds($techId);
        $techIds = $techIdLists[$techId];

        $opportunities = $this->findBillableOpportunities($techIds, $billingPeriodTo);
        $billAdjusts = $this->findUnsettledByAccountId($techId);

        //案件・調整額がなければ.
        if ($opportunities->isEmpty() && $billAdjusts->isEmpty()) {
            return false;
        }

        //精算可能な案件が1つもない、また調整額もなければ.
        $billable = false;
        foreach ($opportunities as $opportunity) {
            if ($this->isBillable($opportunity)) {
                $billable = true;
                break;
            }
        }
        if ($billAdjusts->isNotEmpty()) {
            $billable = true;
        }
        if (!$billable) {
            return false;
        }

        //精算データの作成.
        DB::beginTransaction();
        $bill = Bill::create([
            'account_id' => $techId,
            'billing_period_to' => $billingPeriodTo,
            'bill_step_id' => 1,
            'due_date' => $dueDate
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

        $billData = $this->updateAmount($billId);


        // //端数の処理 100円単位のものを切り上げ
        $amari = $billData->transfer_amount % 1000;
        if ($amari == 0) {

            return $billData;
        } else {

            DB::beginTransaction();
            $billAdjust = BillAdjust::create([
                'amount' => 1000 - $amari,
                'bill_step_id' => 2,
                'bill_id' => $billData->id,
                'account_id' => $techId,
                'note' => "1000円未満の調整額"
            ]);
            DB::commit();

            $billData = $this->updateAmount($billData->id);
            return $billData;
        }

    }

    /**
     * 金額を更新
     *
     * @param [type] $billId
     * @return Object
     */
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

        return $bill;
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
            'opportunity_id' => $opportunity->id
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
     * 子会社のパートナーIDを取得します.
     */
    public function findChildCompanyIds($accountId = null)
    {
        $returnData = h_getChildCompanyIds($accountId);
        return $returnData;
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
}
