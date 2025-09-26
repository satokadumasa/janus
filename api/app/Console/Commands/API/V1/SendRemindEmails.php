<?php

namespace App\Console\Commands\API\V1;

use App\Http\Resources\API\V1\RemindPartnerForCloneResource;
use Illuminate\Console\Command;
use App\Models\API\V1\Opportunity;
use App\Models\API\V1\Partner;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\API\GMO\V1\KousinSaisokuMailToPartner;

class SendRemindEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:remindmails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails of updates to partners';

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
     * パートナーへ更新催促メールを送る
     *
     * @return mixed
     */
    public function handle()
    {
        //毎日入金のパートナーを取得する
        $parents = Partner::where('check_daily_payment', 1)->whereNull("parent_company_id")->pluck('id'); //親会社id
        $branches = Partner::whereIn('parent_company_id', $parents)->pluck('id'); //親に紐づく子会社id
        $accountIds = $parents->concat($branches);
        $nowDate = Carbon::now()->format('Y-m-d H:i:s');


        $accounts = Partner::whereIn('id', str_getcsv($accountIds))
            ->whereHas('opportunities', function ($oppQuery) {
                $oppQuery->whereIn('status_id', [2, 3, 4]);
            })
            ->with(['opportunities' => function ($reQuery) {
                $reQuery->whereIn('status_id', [2, 3, 4]);
            }])
            ->get();


        $targetData = [];

        foreach ($accounts as $account) {
            $targetData[$account->id]['account'] = $account;

            if ($account->opportunities->isNotEmpty()) {
                //collectionに対してのフィルター
                foreach ($account->opportunities as $oppo) {

                    if ($oppo->status_id == 4 && $oppo->worked_date == null && $oppo->work_date > $nowDate) {
                        //工事日がnullで出張確定日が未来の案件は除く
                    } else {
                        $targetData[$account->id]['opportunities'][] = $oppo;
                    }
                }
            }
        }


        //対象のopportuityがあるなら更新督促メールを送る
        foreach ($targetData as $idKey => $value) {

            if (!empty($value['opportunities'])) {
                $this->sendReminderMail($value['account'], $value['opportunities']);
            }
        }
    }

    public function sendReminderMail($account, $opportunities)
    {
        $to = [];

        if(!empty($account->email1)){
            $to[] = $account->email1;
        }
        if(!empty($account->opportunity_email1)){
            $to[] = $account->opportunity_email1;
        }
        if(!empty($account->opportunity_email2)){
            $to[] = $account->opportunity_email2;
        }
        if(!empty($account->opportunity_email3)){
            $to[] = $account->opportunity_email3;
        }

        $partner_name = $account->store_name;

        $sendEmail = Mail::to($to)->bcc(config('app.self_email'), 'mail to self')->send(new KousinSaisokuMailToPartner($opportunities, $partner_name));
    }
}
