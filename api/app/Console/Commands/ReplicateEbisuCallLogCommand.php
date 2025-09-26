<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\API\V1\Opportunity;
use App\Models\API\V1\EbisuLog;
use App\Models\API\V1\OpportunityEbisuLog;
use Carbon\Carbon;

class ReplicateEbisuCallLogCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:replicate_ebisu_callLog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
            \Log::debug("ReplicateEbisuCallLogCommand::handle() START");
            $referrer_domains = [
            'google_ads' => 'www.google.com',
            'google_adwords' => 'www.google.com',
            'google_p_max' => 'www.google.com',
            'yahoo_ads' => 'www.yahoo.co.jp',
            'yahoo_promotion' => 'www.yahoo.co.jp',
        ];
        try {
            $crm_api_url = config('app.ebisu.api_base_url') . "/api/v1/get_ebisu_log";
            $conn = curl_init();    //  cURLセッションの初期化
            curl_setopt($conn, CURLOPT_URL, $crm_api_url);  //  取得するURLを指定
            curl_setopt($conn, CURLOPT_RETURNTRANSFER, true);   //  実行結果を文字列で返す。
            $response =  curl_exec($conn);
            curl_close($conn); //   セッションの終了
            $datas = json_decode($response);
            foreach($datas as $datum) {
				$start_time = $datum->call_log->start_time ? new Carbon($datum->call_log->start_time) : null;
				$end_time = $datum->call_log->end_time ? new Carbon($datum->call_log->end_time) : null;
				if($start_time && $end_time){
					$call_duration = $start_time->diffInSeconds($end_time);
				}else{
					$call_duration = null;
				}
                DB::beginTransaction();
                $ebisu_log = EbisuLog::updateOrCreate([
					'evaluation_id'       => $datum->id,
				], [
					'call_id'             => $datum->call_log_id,
					'opportunity_id'      => $datum->call_log ? $datum->call_log->opportunity_id: null,
					'dial_id'             => $datum->dial_id,
					'inquiry_type_id'     => $datum->call_log ? $datum->call_log->inquiry_type_id: null,
					'visited_at'          => $datum->visit_log ? $datum->visit_log->in_time : null,
					'called_at'           => $start_time,
					'media'               => $datum->visit_log ? $datum->visit_log->utm_medium :null,
					'source'              => $datum->visit_log ? $datum->visit_log->utm_source :null,
					'keyword'             => $datum->visit_log ? $datum->visit_log->keyword :null,
					'call_duration'       => $call_duration,
					'caller_phone_number' => $datum->call_log ? $datum->call_log->c_phone: null,
					'device_type'         => $datum->visit_log ? $datum->visit_log->device : null,
					'caused_url'          => $datum->visit_log ? $datum->visit_log->refere : null,
					'matchtype'           => $datum->visit_log ? $datum->visit_log->matchtype : null,
					'referrer_domain'     => $datum->visit_log ? isset($referrer_domains[$datum->visit_log->utm_source]) ? $referrer_domains[$datum->visit_log->utm_source] : $datum->visit_log->utm_source : null,
					'campaignid'          => $datum->visit_log ? $datum->visit_log->campaignid : null,
					'adgroupid'           => $datum->visit_log ? $datum->visit_log->adgroupid : null,
					'proceeds'            => $datum->value && $datum->value > 1 ? $datum->value : 0,
                ]);


                $ebisu_log->save();
                //  コールログと案件情報のリレーション作成
                // $this->relationCallLog($ebisu_log);
                OpportunityEbisuLog::updateOrCreate([
                    'opportunity_id' => $ebisu_log->opportunity_id,
                    'ebisu_log_id'   => $ebisu_log->id,
                ], [
					'valuation'      => $ebisu_log->proceeds > 0 ? 1.00 : 0.00,
				]);
                DB::commit();
            }
        } catch (\Throwable $th) {
            \Log::debug("ReplicateEbisuCallLogCommand::handle() Error:" . $th->getMessage());
        	DB::rollback();
        }

        return 0;
    }

	public function relationCallLog($ebisu_log){
		\Log::debug("ReplicateEbisuCallLogCommand::relationCallLog() ");
		// 案件との紐づけ処理
		// 一か月前までさかのぼって検索して紐づけ
		$from = Carbon::yesterday()->subDays(30)->format('Y-m-d 00:00:00');
		$oppos = Opportunity::where('id', $ebisu_log->opportunity_id)
			->where('dial_id', $ebisu_log->dial_id)
			->where('created', '>=', $from)
			->whereNotIn('id', function($query) use ($from) {
				$query->select('child_opportunity_id')
					->from('app_opportunity_relations')
					->where('created_at', '>=', $from);
			})
			->get();

        DB::beginTransaction();
        OpportunityEbisuLog::updateOrCreate([
            'ebisu_log_id' => $ebisu_log->id,
            'opportunity_id' => $ebisu_log->opportunity_id,
        ], []);
        DB::commit();
		\Log::debug("ReplicateEbisuCallLogCommand::relationCallLog() End");
		return;
	}
}
