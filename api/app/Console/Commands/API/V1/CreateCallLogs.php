<?php

namespace App\Console\Commands\API\V1;

use App\Models\API\V1\Opportunity;
use App\Models\API\V1\CallLog;
use App\Models\API\V1\OpportunityCallLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateCallLogs extends Command
{
    /**
     * The name and signature of the consolee command.
     *
     * @var string
     */
    protected $signature = 'create:callLogs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'techs liquidation data';

	protected $accessToken;
	protected $refreshToken;
	protected $expiresIn;
	protected $api_base_url;
	protected $login_sid;
	protected $login_email;
	protected $login_password;


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

		$this->api_base_url = config('app.lograph.api_url');
		$this->login_sid = config('app.lograph.login_sid');
		$this->login_email = config('app.lograph.login_email');
		$this->login_password = config('app.lograph.login_password');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
		$this->lographAuth(false);
		\Log::debug("CreateCallLogs::hadle() start");

		$me_url = $this->api_base_url . "/me";
		$header = [
			"Authorization: Bearer {$this->accessToken}",
		];
		$me = $this->curl('GET', $me_url, $header, null); // キャンペーンIDの取得

		$campaigns = $me->_embedded->campaigns;
		$call_log_url = $this->api_base_url . "/behaviors/phone/calls";
		$beginTimestamp = strtotime(Carbon::yesterday()->format('Y-m-d 00:00:00'));
		$endTimestamp = strtotime(Carbon::yesterday()->format('Y-m-d 23:59:59'));
		foreach($campaigns as $campaign){
			$header = [
				"Authorization: Bearer {$this->accessToken}",
			];
			$params = [
				"campaignId" => $campaign->campaignId,
				"beginTimestamp" => $beginTimestamp,
				"endTimestamp" => $endTimestamp,
				"limit" => 100,
				"page" => 1
			];
			$is_remain = true;
			$count_sum = 0;

			$dial_id = $this->extractDialId($campaign);

			while($is_remain){
				$call_logs = $this->curl('GET', $call_log_url, $header, $params); // キャンペーンIDの取得
				foreach($call_logs->_embedded->calls as $call){
					$call_log = $this->insertCallLog($dial_id, $call);
					$this->relationCallLog($call_log);
				}
				$params['page']++;
				$count_sum += $call_logs->count;
				if($call_logs->total <= $count_sum) $is_remain = false;
			}
		}

		$this->hitCall();
		\Log::debug("CreateCallLogs::hadle() end");
	}

	public function hitCall(){
		\Log::debug("CreateCallLogs::hitCall() start");
		// リレーションされていて評価値が合計１に満たない３０日以内に作成された工事完了の案件IDをすべて取得
		$from = Carbon::yesterday()->subDays(30)->format('Y-m-d 00:00:00');
		$oppo_ids = OpportunityCallLog::select(DB::raw('opportunity_id, SUM(valuation) AS valuation_sum'))
			->whereHas('opportunity', function ($query) use ($from) {
				$query->where('status_id', 5)
					->where('created', '>=', $from);
			})
			->groupBy('opportunity_id')
			->having('valuation_sum', '<', 1)
			->get();
		// 順番に確認していく
		foreach($oppo_ids as $oppo){
			$sum = OpportunityCallLog::where('opportunity_id', $oppo->opportunity_id)->sum('valuation');
			if($sum >= 1){
				continue;
			}
			$call_logs = CallLog::whereHas('opportunites', function ($query) use ($oppo) {
				$query->where('app_opportunities.id', $oppo->opportunity_id);
			})
			->orderBy('called_at', 'ASC')
			->get();
			$cc = 0;
			$hit_id = null;
			foreach($call_logs as $log){
				// コールログを順に確認していく
				if($cc == 0){ // とりあえず初めのコールidを取得
					$hit_id = $log->id;
					$cc++;
				}elseif(!empty($log->keyword) && !empty($log->caused_url)){ // ２個目からキーワードとライディングURLが空じゃないものに出会ったら取得しているidを更新
						$hit_id = $log->id;
				}
			}
			// 最終決定したidにヒットコールフラグを付ける
        	DB::beginTransaction();
			OpportunityCallLog::where('call_log_id', $hit_id)
				->where('opportunity_id', $oppo->opportunity_id)
				->update([
					'valuation' => 1.00
				]);
        	DB::commit();
		}
		\Log::debug("CreateCallLogs::hitCall() end");
		return;

	}

	public function relationCallLog($call_log){
		\Log::debug("CreateCallLogs::hadle() START");
		// 案件との紐づけ処理
		// $from = Carbon::yesterday()->format('Y-m-d 00:00:00');
		// $to = Carbon::yesterday()->format('Y-m-d 23:59:59');
		// 一か月前までさかのぼって検索して紐づけ
		$from = Carbon::yesterday()->subDays(30)->format('Y-m-d 00:00:00');
		// $to = Carbon::yesterday()->subDays(30)->format('Y-m-d 23:59:59');
		$oppos = Opportunity::where('incoming_phone', $call_log->caller_phone_number)
			->where('dial_id', $call_log->dial_id)
			->where('created', '>=', $from)
			->whereNotIn('id', function($query) use ($from) {
				$query->select('child_opportunity_id')
					->from('app_opportunity_relations')
					->where('created_at', '>=', $from);
			})
			->get();
		foreach($oppos as $oppo){
        	DB::beginTransaction();
			OpportunityCallLog::updateOrCreate([
				'call_log_id' => $call_log->id,
				'opportunity_id' => $oppo->id,
			], []);
        	DB::commit();
		}
		return;
	}

	public function insertCallLog($dial_id, $call){
		\Log::debug("CreateCallLogs::hadle() START");
		$call_log = CallLog::where('call_id', $call->callId)->first();
		if(empty($call_log)){
			$campaignid = null;
			$adgroupid = null;
			if(!empty($call->causedUrl)){
				$param_arr = explode("&", $call->causedUrl);
				foreach($param_arr as $param){
					$params = explode("=", $param);
					if(isset($params[1]) && $params[0] == 'campaignid'){
						$campaignid = $params[1];
					}elseif(isset($params[1]) && $params[0] == 'adgroupid'){
						$adgroupid = $params[1];
					}
				}
			}
        	DB::beginTransaction();
			$call_log = CallLog::create([
				'dial_id' => $dial_id,
				'visited_at' => $this->dateFormat($call->visitedAt),
				'called_at' => $this->dateFormat($call->calledAt),
				'observer_id' => $call->observerId,
				'observer_label' => $call->observerLabel,
				'audience_id' => $call->audienceId,
				'call_id' => $call->callId,
				'media' => $call->media,
				'source' => $call->source,
				'keyword' => $call->keyword,
				'page_view' => $call->pageView,
				'call_duration' => $call->callDuration,
				'caller_phone_number' => $call->callerPhoneNumber,
				'tracking_phone_number' => $call->trackingPhoneNumber,
				'hangup_code' => $call->hangupCode,
				'device_type' => $call->deviceType,
				'caused_url' => $call->causedUrl,
				'recorded_audio_url' => $call->recordedAudioUrl,
				'content' => $call->content,
				'matchtype' => $call->matchtype,
				'referrer_domain' => $call->referrerDomain,
				'campaignid' => $campaignid,
				'adgroupid' => $adgroupid,
			]);
        	DB::commit();
		}
		return $call_log;
	}

	public function extractDialId($campaign){
		if(preg_match('/[0-9]+/u', $campaign->label, $result)){
			\Log::debug("CreateCallLogs::extractDialId() match");
			return intval($result[0]);
		}
		return null;

	}

	public function dateFormat($date_str){
		$date = null;
		if(!empty($date_str)){
			$unix = strtotime($date_str);
			$date = date('Y-m-d H:i:s', $unix);
		}
		return $date;
	}

	public function lographAuth($is_refresh){
		$url = $this->api_base_url . "/authentications";
		if($is_refresh && !empty($this->refreshToken)){
			$method = 'PUT';
			$header = [
				"Content-type: application/json",
				"Accept: application/json",
			];
			$params = [
				"refreshToken" => $this->refreshToken,
			];
		}else{
			$method = 'POST';
			$header = [
				"Content-type: application/json",
				"Accept: application/json",
			];
			$params = [
				"sid" => $this->login_sid,
				"email" => $this->login_email,
				"password" => $this->login_password
			];
		}
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
		$result_json = curl_exec($curl);
		$errno = curl_errno($curl);
		curl_close($curl);
		if($errno !== CURLE_OK){
			\Log::debug("CreateCallLogs::lographAuth() errno: " . print_r($errno, true));
			return false;
		} else {
			$auth = json_decode($result_json);
			$this->accessToken = $auth->accessToken;
			$this->refreshToken = $auth->refreshToken;
			$this->expiresIn = $auth->expiresIn;
			return;
		}
	}

	public function curl($method, $url, $header, $params){
		$now = Carbon::now()->format('Y-m-d H:i:s');
		$now_unix = strtotime($now);
		if($now_unix > $this->expiresIn){
			$this->lographAuth(true);
		}
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
		if($method == 'GET'){
			$parameter = '';
			if(!empty($params)){
				foreach($params as $key => $param){
					if(!empty($param)){
						if($parameter == ''){
							$parameter = "?" . $key . "=" . urlencode($param);
						}else{
							$parameter .= "&" . $key . "=" . urlencode($param);
						}
					}
				}
			}
			curl_setopt($curl, CURLOPT_URL, $url . $parameter);
		}else{
			curl_setopt($curl, CURLOPT_URL, $url);
			if(!empty($params)){
				curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
			}
		}
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
		$result_json = curl_exec($curl);
		$errno = curl_errno($curl);
		curl_close($curl);

		if($errno !== CURLE_OK){
			\Log::debug("CreateCallLogs::curl() errno: " . print_r($errno, true));
			return false;
		} else {
			\Log::debug("CreateCallLogs::curl() curl success (" . $url . ")");
			return json_decode($result_json);
		}
	}
}