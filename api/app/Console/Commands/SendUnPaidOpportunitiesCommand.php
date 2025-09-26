<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\API\V1\Opportunity;
use App\Services\ChatWorkService;


class SendUnPaidOpportunitiesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:SendUnPaidOpportunitiesCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '未精算案件の送信';

    protected $ebisu_api_base_url = null;
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
		\Log::debug("SendUnPaidOpportunitiesCommand::handle() start");
		$storagePath = storage_path();
		if(!file_exists($storagePath . '/app/unpaid')) {
			mkdir($storagePath . '/app/unpaid');
		}

		$now = Carbon::now()->format('Ymd');
        $path = "app/unpaid/{$now}_unpaid.csv";
		$file_path = "unpaid/{$now}_unpaid.csv";
		// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		$header = [
			"受付日",
			"案件番号",
			"案件ステータス",
			"工事完了日",
			"借方取引先コード",
			"借方取引先名",
			"貸方金額（税込み）",
			"領収状況",
			"登録売上",
		];
		$handle = fopen(storage_path($path), 'w');
		fwrite($handle, pack('C*', 0xEF, 0xBB, 0xBF));
		fputcsv($handle, $header);
		fclose($handle);

		Opportunity::whereIn('id', function($sub_query){
				$sub_query->select('opportunity_id')
					->from('app_receipts')
					->whereNotIn('payment_method_id', [8, 9, 10])
					->where(function($query){
						$query->where('payment_method_id', '<>', 1)
							->orWhere('by_kurapital', '<>', 0);
					});
			})
			->whereIn('id', function($sub_query){
				$sub_query->select('opportunity_id')
					->from('app_companies_opportunities')
					->whereIn('company_id', function($sub_sub_query){
						$sub_sub_query->select('id')
							->from('app_companies');
					});
			})
			->where('receipt_date', '>=', '2021-07-01 00:00:00')
			// ->whereBetween('receipt_date', ['2021-07-01 00:00:00', '2022-07-01 00:00:00']) // TEST
			->where('dial_id', '<>', 29)
			->orderBy('id')
			->with('status', 'CompanyOpportunity', 'CompanyOpportunity.Company', 'ReceiptStatusDetail', 'receipts')
			->chunkById(1000, function ($opportunities) use ($path){
        		$handle = fopen(storage_path($path), 'a');
				foreach($opportunities as $opportunity){
					$row = [];
					$row[] = $opportunity->receipt_date;
					$row[] = $opportunity->id;
					$row[] = $opportunity->status->name;
					$row[] = $opportunity->worked_date;
					$row[] = 'C' . sprintf('%05d', $opportunity->CompanyOpportunity->company_id);
					$row[] = $opportunity->CompanyOpportunity->Company->name;
					$sum_amount = 0;
					foreach($opportunity->receipts as $receipt){
						if(!in_array($receipt->payment_method_id, [8, 9, 10]) && ($receipt->payment_method_id != 1 || $receipt->by_kurapital != 0)){
							$sum_amount += $receipt->amount;
						}
					}
					$row[] = $sum_amount;
					$row[] = !empty($opportunity->ReceiptStatusDetail) ? $opportunity->ReceiptStatusDetail->name : '';
					$row[] = $opportunity->sales;
                	fputcsv($handle, $row);
				}
        		fclose($handle);
			});
		chmod(storage_path($path), 0777);
		// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
		$chatGroupId = 336821707;
		$to_users = [
			6820790 => '河田',
			7862139 => '江澤'
		];

		$currentDate = Carbon::now();
		$lastMonthDate = $currentDate->subMonth()->format('Y年m月');

		$message = "お疲れ様です。\n{$lastMonthDate}末時点の未入金一覧になります。\n\nご確認の程宜しくお願いいたします。";
		ChatWorkService::sendFiles($chatGroupId, $to_users, $message, storage_path($path));
		\Log::debug("SendUnPaidOpportunitiesCommand::handle() end");
    }
}
