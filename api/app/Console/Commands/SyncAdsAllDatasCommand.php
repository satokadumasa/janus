<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\API\V1\Opportunity;
use App\Models\API\V1\EbisuLog;
use App\Models\API\V1\OpportunityEbisuLog;
use Carbon\Carbon;

use App\Models\API\V1\AdsAccount;
use App\Models\API\V1\AdsCampaign;
use App\Models\API\V1\AdsAdGroup;
use App\Models\API\V1\AdsMetric;

use App\Models\API\V1\Campaign;
use App\Models\API\V1\Adgroup;

class SyncAdsAllDatasCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:SyncAdsAllDatasCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ebisu AdsAllDatas';

    protected $ebisu_api_base_url = null;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
		$this->ebisu_api_base_url = config('app.ebisu.api_base_url');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		\Log::debug("SyncAdsAllDatasCommand::handle() start");
		$now_date = Carbon::now()->format('Y-m-d');
		$this->syncAdsAccounts();
		$this->syncAdsAdGroups();
		$this->syncAdsCampaigns();
		$this->syncAdsMetrics();

		$this->updateOldCampaigns();
		$this->updateOldAdGroups();

		\Log::debug("SyncAdsAllDatasCommand::handle() end");
    }

	public function updateOldCampaigns(){
		\Log::debug("SyncAdsAllDatasCommand::updateOldCampaigns() start");
		AdsCampaign::whereNotIN('campaign_id', function($query){
				$query->select('id')
					->from('app_campaigns');
			})
			->chunkById(1000, function ($adsCampaigns){
			foreach($adsCampaigns as $adsCampaign){
				DB::beginTransaction();
				Campaign::updateOrCreate([
					'id'   => $adsCampaign->campaign_id
				], [
					'name' => $adsCampaign->name
				]);
				DB::commit();
			}
		});
		\Log::debug("SyncAdsAllDatasCommand::updateOldCampaigns() end");
	}


	public function updateOldAdGroups(){
		\Log::debug("SyncAdsAllDatasCommand::updateOldAdGroups() start");
		AdsAdGroup::whereNotIN('ad_group_id', function($query){
				$query->select('id')
					->from('app_adgroups');
			})
			->chunkById(1000, function ($adsAdGroups){
			foreach($adsAdGroups as $adsAdGroup){
				DB::beginTransaction();
				Adgroup::updateOrCreate([
					'id'   => $adsAdGroup->ad_group_id
				], [
					'name' => $adsAdGroup->name
				]);
				DB::commit();
			}
		});
		\Log::debug("SyncAdsAllDatasCommand::updateOldAdGroups() end");
	}

	public function syncAdsAccounts(){
		\Log::debug("SyncAdsAllDatasCommand::syncAdsAccounts() start");
		$ebisu_api_url = $this->ebisu_api_base_url . "/api/v1/get_ads_accounts";
		$params = [
			'page' => 1,
		];
		$repeat = true;
		while($repeat){
			$repeat = false;
			$accounts = $this->requestToEbisu($ebisu_api_url, $params);
			foreach($accounts as $account){
				$repeat = true;
				DB::beginTransaction();
				AdsAccount::updateOrCreate([
					'platform'         => $account->platform,
					'mcc_account_id'   => $account->mcc_account_id,
					'customer_id'      => $account->customer_id
				], [
					'descriptive_name' => $account->descriptive_name
				]);
				DB::commit();
			}
			$params['page']++;
		}
	}


	public function syncAdsCampaigns(){
		\Log::debug("SyncAdsAllDatasCommand::syncAdsCampaigns() start");
		$ebisu_api_url = $this->ebisu_api_base_url . "/api/v1/get_ads_campaigns";
		$params = [
			'page' => 1,
		];
		$repeat = true;
		while($repeat){
			$repeat = false;
			$campaigns = $this->requestToEbisu($ebisu_api_url, $params);
			foreach($campaigns as $campaign){
				$repeat = true;
				DB::beginTransaction();
				AdsCampaign::updateOrCreate([
					'platform'    => $campaign->platform,
					'customer_id' => $campaign->customer_id,
					'campaign_id' => $campaign->campaign_id
				], [
					'name'        => $campaign->name
				]);
				DB::commit();
			}
			$params['page']++;
		}
	}


	public function syncAdsAdGroups(){
		\Log::debug("SyncAdsAllDatasCommand::syncAdsAdGroups() start");
		$ebisu_api_url = $this->ebisu_api_base_url . "/api/v1/get_ads_ad_groups";
		$params = [
			'page' => 1,
		];
		$repeat = true;
		while($repeat){
			$repeat = false;
			$ad_groups = $this->requestToEbisu($ebisu_api_url, $params);
			foreach($ad_groups as $ad_group){
				$repeat = true;
				DB::beginTransaction();
				AdsAdGroup::updateOrCreate([
					'platform'    => $ad_group->platform,
					'campaign_id' => $ad_group->campaign_id,
					'ad_group_id' => $ad_group->ad_group_id
				], [
					'name'        => $ad_group->name
				]);
				DB::commit();
			}
			$params['page']++;
		}
	}


	public function syncAdsMetrics(){
		\Log::debug("SyncAdsAllDatasCommand::syncAdsMetrics() start");
		$ebisu_api_url = $this->ebisu_api_base_url . "/api/v1/get_ads_metrics";
		$params = [
			'date' => Carbon::yesterday()->format('Y-m-d'),
			'page' => 1,
		];
		$repeat = true;
		while($repeat){
			$repeat = false;
			$metrics = $this->requestToEbisu($ebisu_api_url, $params);
			foreach($metrics as $metric){
				$repeat = true;
				DB::beginTransaction();
				AdsMetric::updateOrCreate([
					'platform'                             => $metric->platform,
					'report_date'                          => $metric->report_date,
					'customer_id'                          => $metric->customer_id,
					'campaign_id'                          => $metric->campaign_id,
					'ad_group_id'                          => $metric->ad_group_id,
					'device'                               => $metric->device
				], [
					'impressions'                          => $metric->impressions,
					'clicks'                               => $metric->clicks,
					'ctr'                                  => $metric->ctr,
					'cpc'                                  => $metric->cpc,
					'conversions'                          => $metric->conversions,
					'conversions_value'                    => $metric->conversions_value,
					'cost'                                 => $metric->cost,
					'cpa'                                  => $metric->cpa,
					'search_absolute_top_impression_share' => $metric->search_absolute_top_impression_share
				]);
				DB::commit();
			}
			$params['page']++;
		}
	}

	public function requestToEbisu($ebisu_api_url, $params){
		\Log::debug("SyncAdsAllDatasCommand::requestToEbisu() start");
        try {
			$params = $this->makeParams($params);
			$url = $params ? "{$ebisu_api_url}?{$params}" : $ebisu_api_url;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response =  curl_exec($ch);
			if (curl_errno($ch)) {
				\Log::debug("SyncAdsAllDatasCommand::requestToEbisu() Curl Error Message: " . print_r(curl_error($ch), true));
			}
			curl_close($ch);
			$result = json_decode($response);
			return $result;
        } catch (\Throwable $th) {
            \Log::debug("SyncAdsAllDatasCommand::requestToEbisu() Error:" . $th->getMessage());
        }
	}

	public function makeParams($params_arr){
		$params = null;
		foreach($params_arr as $key => $val){
			if(empty($params)){
				$params = "{$key}={$val}";
			}else{
				$params .= "&{$key}={$val}";
			}
		}
		return $params;
	}
}
