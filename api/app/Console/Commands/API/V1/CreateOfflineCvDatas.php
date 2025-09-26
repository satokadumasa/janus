<?php

namespace App\Console\Commands\API\V1;

use App\Models\API\V1\Opportunity;
use App\Models\API\V1\CallLog;
use App\Models\API\V1\OfflineConversionData;
use App\Models\API\V1\OpportunityRelation;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateOfflineCvDatas extends Command
{
    /**
     * The name and signature of the consolee command.
     *
     * @var string
     */
    protected $signature = 'create:offlineCvDatas';

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
    public function handle(){
		\Log::debug("OfflineCvDatas::hadle() start");
		$from = Carbon::yesterday()->subDays(30)->format('Y-m-d 00:00:00');
		// コールデータを漁る
		$call_logs = CallLog::where('called_at', '>=', $from)
			->with(['opportunityCallLogs', 'opportunityCallLogs.opportunity', 'opportunityCallLogs.opportunity.partnerDetail', 'OfflineConversionData'])
			->chunkById(1000, function($call_logs){
				foreach($call_logs as $call_log){
					$opportunity_id = null;
					$value = 0;
					$clid_name = "";
					$clid = "";
					if(strpos($call_log->source,'google') !== false){
						$clid_name = 'gclid';
					}elseif(strpos($call_log->source,'yahoo') !== false){
						$clid_name = 'yclid';
					}
					$url_array = explode("?", $call_log->caused_url);
					if(isset($url_array[1])){
						$url_array = explode("&", $url_array[1]);
						foreach($url_array as $block){
							$params = explode("=", $block);
							if(isset($params[1]) && $params[0] == $clid_name){
								$check = explode("#", $params[1]);
								$clid = $check[0];
							}
						}
					}
					if(count($call_log->opportunityCallLogs) > 0){
						foreach($call_log->opportunityCallLogs as $oppoCall){
							if(!empty($oppoCall->opportunity)){
								$opportunity_id = $oppoCall->opportunity->id;
								if($oppoCall->valuation > 0  && in_array($oppoCall->opportunity->status_id, [5, 7, 8])){
									$value += $this->calAccountingSales($oppoCall->opportunity) * $oppoCall->valuation;
								}
								// // 追加案件分を追加
								// $relations = OpportunityRelation::where('parent_opportunity_id', $opportunity_id)
								// 	->with('ChildOpportunity')
								// 	->get();
								// foreach($relations as $data){
								// 	if(in_array($data->ChildOpportunity->status_id, [5, 7, 8])){
								// 		$value += $this->calAccountingSales($data->ChildOpportunity);
								// 	}
								// }
								$value += $this->getAdjoinOppoSales([$opportunity_id]);
							}
						}
					}
					if(!empty($call_log->dial_id) && !empty($call_log->source) && $clid_name != "" && $clid != "" && !empty($call_log->called_at)){
						if(!empty($call_log->OfflineConversionData) && $call_log->OfflineConversionData->value > 0 && $value == 0){
							// 既にCVテーブルが作成されていて今回のアップデートで売上が0になっている場合
							// 消さないように１を入れる
							$value = 1;
						}
						DB::beginTransaction();
						OfflineConversionData::updateOrCreate(
							[
								'cv_action_name' => 'コールコンバージョン',
								'dial_id' => $call_log->dial_id,
								'source' => $call_log->source,
								'clid_name' => $clid_name,
								'clid' => $clid,
								'cv_date' => $call_log->called_at,
								'call_log_id' => $call_log->id,
							],[
								'opportunity_id' => $opportunity_id,
								'value' => $value,
							]
						);
						DB::commit();
					}
				}
			});

		$mail_opportunities = Opportunity::where('created', '>=', $from)
			->whereNotNull('refere')
			->with(['partnerDetail', 'OfflineConversionData'])
			->get();
		foreach($mail_opportunities as $mail_opportunity){
			$clid_name = "";
			$clid = "";
			$source = "";
			if(strpos($mail_opportunity->refere,'google') !== false){
				$clid_name = 'gclid';
			}elseif(strpos($mail_opportunity->refere,'yahoo') !== false){
				$clid_name = 'yclid';
			}
			$url_array = explode("?", $mail_opportunity->refere);
			if(isset($url_array[1])){
				$url_array = explode("&", $url_array[1]);
				foreach($url_array as $block){
					$params = explode("=", $block);
					if(isset($params[1]) && $params[0] == $clid_name){
						$check = explode("#", $params[1]);
						$clid = $check[0];
					}elseif(isset($params[1]) && $params[0] == 'utm_source'){
						$source = $params[1];
					}
				}
			}
			if(!empty($mail_opportunity->dial_id) && $clid_name != "" && $clid != "" && $source != ""){
				// $opportunity_id = null;
				$opportunity_id = $mail_opportunity->id;
				$value = 0;
				if(in_array($mail_opportunity->status_id, [5, 7, 8])){
					$value = $this->calAccountingSales($mail_opportunity);
				}
				// 追加案件分を追加
				// $relations = OpportunityRelation::where('parent_opportunity_id', $opportunity_id)
				// 	->with('ChildOpportunity')
				// 	->get();
				// foreach($relations as $data){
				// 	if(in_array($data->ChildOpportunity->status_id, [5, 7, 8])){
				// 		$value += $this->calAccountingSales($data->ChildOpportunity);
				// 	}
				// }
				$value += $this->getAdjoinOppoSales([$opportunity_id]);
				if(!empty($mail_opportunity->OfflineConversionData) && $mail_opportunity->OfflineConversionData->value > 0 && $value == 0){
					// 既にCVテーブルが作成されていて今回のアップデートで売上が0になっている場合
					// 消さないように１を入れる
					$value = 1;
				}
                DB::beginTransaction();
				OfflineConversionData::updateOrCreate(
					[
						'cv_action_name' => 'メールコンバージョン',
						'dial_id' => $mail_opportunity->dial_id,
						'source' => $source,
						'clid_name' => $clid_name,
						'clid' => $clid,
						'cv_date' => $mail_opportunity->created,
						'call_log_id' => null,
					],[
						'opportunity_id' => $opportunity_id,
						'value' => $value,
					]
				);
                DB::commit();
			}
		}
		\Log::debug("OfflineCvDatas::hadle() end");
	}


	function calAccountingSales($opportunity){
		// 経理売上の計算
		$accouting_sales = 0;
		$sales = isset($opportunity->sales) ? $opportunity->sales : 0;
		$material_cost = isset($opportunity->material_cost) ? $opportunity->material_cost : 0;
		$other_cost = isset($opportunity->other_cost) ? $opportunity->other_cost : 0;
		$share = isset($opportunity->account_id) ? $opportunity->partnerDetail->share : 0;
		$share_method_id = isset($opportunity->account_id) ? $opportunity->partnerDetail->share_method_id : 0;
		// if($opportunity->id == 1561462){
		// 	\Log::debug("sales: {$sales}" );
		// 	\Log::debug("material_cost: {$material_cost}" );
		// 	\Log::debug("other_cost: {$other_cost}" );
		// 	\Log::debug("share: {$share}" );
		// 	\Log::debug("share_method_id: {$share_method_id}" );
		// }
		if(isset($opportunity->account_id) && $sales != 0){
			if($share_method_id == 1){
				$accouting_sales = ($sales - $material_cost - $other_cost) * (1.0-$share);
			}elseif($share_method_id == 2){ // 自社部隊はここ
				$accouting_sales = ($sales - $other_cost) * (1.0-$share);
			}elseif($share_method_id == 3){
				$kurapital = ($sales - $other_cost) * (1.0-$share);
				$partner = ($sales - $other_cost) * $share - $material_cost;
				if($kurapital <= $partner) {
					$accouting_sales = ($sales - $other_cost) * (1.0-$share);
				} else {
					$sales2 = $sales - $other_cost;
					$cost = ($sales2 + $material_cost - 2 * $share * $sales2)/(2 - 2 * $share);
					$accouting_sales = ($sales - $cost - $other_cost) * (1.0-$share);
				}
			}
		}
		$result = $accouting_sales != 0 ? floor($accouting_sales / 110 * 100) : 0;
		return $result;
	}

	function getAdjoinOppoSales($ids){
		$value = 0;
		$opportunity_ids = $ids;
		while(!empty($opportunity_ids) && count($opportunity_ids) > 0){
			$relations = OpportunityRelation::whereIn('parent_opportunity_id', $opportunity_ids)
				->with('ChildOpportunity')
				->get();
			$opportunity_ids = [];
			foreach($relations as $relation){
				if(!empty($relation->ChildOpportunity)){
					if(in_array($relation->ChildOpportunity->status_id, [5, 7, 8])){
						$value += $this->calAccountingSales($relation->ChildOpportunity);
					}
					$opportunity_ids[] = $relation->ChildOpportunity->id;
				}
			}
		}
		return $value;
	}
}