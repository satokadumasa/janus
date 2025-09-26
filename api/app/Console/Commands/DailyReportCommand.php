<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\API\GMO\V1\Kurapital\Opportunity;
use App\Models\API\V1\Opportunity AS Oppo;
use App\Models\API\V1\BuisinessPlan;
use App\Services\ChatWorkService;


class DailyReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:daily_report {--sub_day=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '法人日報';

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

     protected $subDay = 0;
    public function handle()
    {
        \Log::debug("TaskService::handle() Start");
        $params['sub_day'] = $this->option('sub_day');
        \Log::debug("TaskService::handle() subday[{$params['sub_day']}]");
        $params['dial_id'] = 9;
        $houjin = self::countTask($params);
        $params['dial_id'] = 76;
        $btob = self::countTask($params);

        //  完了率
        $worked_date_month_cnt = $houjin['worked_date_month_cnt'] + $btob['worked_date_month_cnt'];
        $visit_total = $houjin['work_date_month_cnt'] + $btob['work_date_month_cnt'];
        \Log::debug("TaskService::handle() visit_total[{$visit_total}] worked_date_month_cnt[{$worked_date_month_cnt}]");
        $worked_rate = $visit_total != 0 ? $worked_date_month_cnt / $visit_total : 0;
        $worked_rate = $worked_rate * 100;
        \Log::debug("TaskService::handle() worked_rate[{$worked_rate}]");
        //  予算・目標額取得
        Carbon::setLocale('ja'); 
        $datestamp = Carbon::now()->isoFormat('MM/DD(ddd)');
        $plan_ym = Carbon::today()->format('Ym');
        $buisiness_plan = BuisinessPlan::where('plan_ym', $plan_ym)->first();
        //  達成率
        $work_date_month_sum = $houjin['work_date_month_sum'] + $btob['work_date_month_sum'];
        $achievement_rate = $buisiness_plan && $buisiness_plan->budget ? $work_date_month_sum / $buisiness_plan->budget : 0;
        $achievement_rate = $achievement_rate * 100;
        \Log::debug("TaskService::handle() achievement_rate[{$achievement_rate}]");

        $data = [
            'datestamp'                  => $datestamp,
            'visit_today_1'              => number_format($houjin['work_date_today_cnt'] ? $houjin['work_date_today_cnt'] : 0, 0, '.', ','),
            'visit_today_2'              => number_format($btob['work_date_today_cnt'] ? $btob['work_date_today_cnt'] : 0, 0, '.', ','),
            'visit_today'                => number_format($houjin['work_date_today_cnt'] + $btob['work_date_today_cnt'], 0, '.', ','),
            'visit_total_1'              => number_format($houjin['work_date_month_cnt'], 0, '.', ','),
            'visit_total_2'              => number_format($btob['work_date_month_cnt'], 0, '.', ','),
            'visit_total'                => number_format($houjin['work_date_month_cnt'] + $btob['work_date_month_cnt'], 0, '.', ','),
            'worked_today_1'             => number_format($houjin['worked_date_today_cnt'], 0, '.', ','),
            'worked_today_2'             => number_format($btob['worked_date_today_cnt'] , 0, '.', ','),
            'worked_today'               => number_format($houjin['worked_date_today_cnt'] + $btob['worked_date_today_cnt'], 0, '.', ',') ,
            'worked_total_1'             => number_format($houjin['worked_date_month_cnt'], 0, '.', ','),
            'worked_total_2'             => number_format($btob['worked_date_month_cnt'], 0, '.', ',') ,
            'worked_total'               => number_format($houjin['worked_date_month_cnt'] + $btob['worked_date_month_cnt'], 0, '.', ',') ,
            'worked_rate'                => number_format($worked_rate, 2, '.', ','),
            'budget'                     => number_format($buisiness_plan->budget, 0, '.', ','),
            'sales_today_1'              => number_format($houjin['work_date_today_sum'] ? $houjin['work_date_today_sum'] : 0, 0, '.', ','),
            'sales_today_2'              => number_format($btob['work_date_today_sum'] ? $btob['work_date_today_sum'] : 0, 0, '.', ','),
            'sales_today'                => number_format($houjin['work_date_today_sum'] + $btob['work_date_today_sum'], 0, '.', ','),
            'sakes_total_1'              => number_format($houjin['work_date_month_sum'], 0, '.', ','),
            'sakes_total_2'              => number_format($btob['work_date_month_sum'], 0, '.', ','),
            'sakes_total'                => number_format($work_date_month_sum, 0, '.', ','),
            'achievement_rate'           => number_format($achievement_rate, 2, '.', ',') ,
            'new_reception_1'            => number_format($houjin['new_reception'], 0, '.', ',') ,
            'new_reception_2'            => number_format($btob['new_reception'], 0, '.', ',') ,
            'new_reception'              => number_format($houjin['new_reception'] + $btob['new_reception'], 0, '.', ',') ,
            'new_cumulative_1'           => number_format($houjin['new_cumulative'], 0, '.', ',') ,
            'new_cumulative_2'           => number_format($btob['new_cumulative'], 0, '.', ',') ,
            'new_cumulative'             => number_format($houjin['new_cumulative'] + $btob['new_cumulative'], 0, '.', ',') ,
            'daily_average_1'            => number_format($houjin['daily_average'], 0, '.', ',') ,
            'daily_average_2'            => number_format($btob['daily_average'], 0, '.', ',') ,
            'daily_average'              => number_format($houjin['daily_average'] + $btob['daily_average'], 0, '.', ',') ,
            'photo_report_count_1'       => number_format($houjin['photo_report_count'], 0, '.', ',') ,
            'photo_report_count_2'       => number_format($btob['photo_report_count'], 0, '.', ',') ,
            'photo_report_count'         => number_format($houjin['photo_report_count'] + $btob['photo_report_count'], 0, '.', ',') ,
            'estimation_report_count_1'  => number_format($houjin['estimation_report_count'], 0, '.', ',') ,
            'estimation_report_count_2'  => number_format($btob['estimation_report_count'], 0, '.', ',') ,
            'estimation_report_count'    => number_format($houjin['estimation_report_count'] + $btob['estimation_report_count'], 0, '.', ',') ,
            'first_adjustment_1'         => number_format($houjin['first_adjustment'], 0, '.', ',') ,
            'first_adjustment_2'         => number_format($btob['first_adjustment'], 0, '.', ',') ,
            'first_adjustment'           => number_format($houjin['first_adjustment'] + $houjin['first_adjustment'], 0, '.', ',') ,
            'construction_adjustment_1'  => number_format($houjin['construction_adjustment'], 0, '.', ',') ,
            'construction_adjustment_2'  => number_format($btob['construction_adjustment'], 0, '.', ',') ,
            'construction_adjustment'    => number_format($houjin['construction_adjustment'] + $btob['construction_adjustment'], 0, '.', ',') ,
            'arrival_1'                  => number_format($houjin['arrival'], 0, '.', ',') ,
            'arrival_2'                  => number_format($btob['arrival'], 0, '.', ',') ,
            'arrival'                    => number_format($houjin['arrival'] + $btob['arrival'], 0, '.', ','),
        ];

        $group_id = config('app.daily_report.group_id');
        $to_ids = config('app.daily_report.to_ids');
        
        $to_ids = self::spliteToIds($to_ids);
        $ids = [];
        foreach($to_ids AS $index => $to_id) {
            $ids[] = $index;
        }
        $to_ids = implode(',', $ids);
        $body = self::variableEmbedding($data);
        \Log::debug("DailyReportCommand::handle body:" . $body);
        // ChatWorkService::sendDailyReport($group_id, $ids[0], $body);
        return 0;
    }

    /**
     * Summary of variableEmbedding
     * @param mixed $dataa
     * @return string
     */
    private static function variableEmbedding($data) {
        \Log::debug("DailyReportCommand::variableEmbedding()");
        $body = "
【法人日報】（1課+2課）
--datestamp--

■工事件数
本日訪問　   --visit_today--（--visit_today_1-- + --visit_today_2--）
訪問累計　   --visit_total--（--visit_total_1-- + --visit_total_2--）

本日完了　   --worked_today--（--worked_today_1-- + --worked_today_2--）
完了累計　   --worked_total--（--worked_total_1-- + --worked_total_2--）
完了率　　   --worked_rate--%

■売上高　目標： --budget--
本日         --sales_today--（--sales_today_1-- + --sales_today_2--）
累計         --sakes_total--（--sakes_total_1-- + --sakes_total_2--）
達成率       --achievement_rate--%

■受付状況
新規受付     --new_reception-- （--new_reception_1-- + --new_reception_2--）
新規累計     --new_cumulative--（--new_cumulative_1-- + --new_cumulative_2--）
１日平均     --daily_average-- （--daily_average_1-- + --daily_average_2--）

■アクション
報告書       --photo_report_count--（--photo_report_count_1-- + --photo_report_count_2--）
見積書       --estimation_report_count--（--estimation_report_count_1-- + --estimation_report_count_2--）
初回調整     --first_adjustment--（--first_adjustment_1-- + --first_adjustment_2--）
施工調整     --construction_adjustment--（--construction_adjustment_1-- + --construction_adjustment_2--）

■明日現場到着
明日現場到着 --arrival--（--arrival_1-- + --arrival_2--）
";
        foreach($data AS $index => $value) {
            $repstr = "--{$index}--";
            $body = str_replace($repstr, $data[$index], $body);
        }

        return $body;
    }

    /**
     * Summary of spliteToIds
     * @param mixed $to_ids
     * @return string[]
     */
    private static function spliteToIds($to_ids) {
        \Log::debug("DailyReportCommand::spliteToIds()");
        $to_ids = explode(',', $to_ids);
        $ids = [];
        foreach($to_ids AS $str) {
            $arr = explode(':', $str);
            $ids[(string)$arr[0]] = $arr[1];
        }
        return $ids;
    }

    /**
     * Summary of countTask
     * @param mixed $params
     * @return array
     */
    private static function countTask($params)
    {
        \Log::debug("TaskService::contTask() Start");

        $result = [
            'report_total_count' => 0,
            'report_today_count' => 0,
            'report_yesterday_count' => 0,
            'work_date_today_cnt' => 0,
            'worked_date_today_cnt' => 0,
            'worked_date_month_cnt' => 0,
            'work_date_today_sum' => 0,
            'work_date_month_sum' => 0,
            'work_date_month_cnt' => 0,
            'work_date_today_profit_sum' => 0,
            'work_date_month_profit_sum' => 0,
            'new_reception' => 0,
            'new_cumulative' => 0 ,
            'daily_average' => 0 ,
            'photo_report_count' => 0 ,
            'estimation_report_count' => 0 ,
            'first_adjustment' => 0 ,
            'construction_adjustment' => 0 ,
            'arrival' => 0 ,
        ];
        $result = [];
        $sub_day = isset($params['sub_day'])  ? $params['sub_day'] : config('app.daily_report.date_of_collection');
        // $tomorrowStart = Carbon::tomorrow()->format('Y-m-d 00:00:00');
        // $tomorrowEnd = Carbon::tomorrow()->format('Y-m-d 23:59:59');
        $tomorrowStart = Carbon::now()->format('Y-m-d 00:00:00');
        $tomorrowEnd = Carbon::now()->format('Y-m-d 23:59:59');
        $todayStart = Carbon::now()->subDay($sub_day)->format("Y-m-d 00:00:00"); 
        $todayEnd = Carbon::now()->subDay($sub_day)->format("Y-m-d 23:59:59");
        \Log::debug("TaskService::contTask() subDay[{$sub_day}] todayStart[{$todayStart}] todayEnd[{$todayEnd}]");

        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d 00:00:00');
        $endOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d 23:59:59');

        //  全ての未読タスク
        $tasks = self::task($params['dial_id']);
        $tasks->whereIn('app_tasks.task_status_id', [1, null]);//nullを検索dd
        $report_total_count = $tasks->count();

        // 本日の未完了の「報告書作成済」のタスク
        $tasks = self::task($params['dial_id']);
        $tasks->where('app_tasks.content_id', 5);
        $tasks->where('app_tasks.task_status_id', 1);//nullを検索
        $report_today_count = $tasks->whereBetween('app_tasks.limit_date', [$todayStart, $todayEnd])->count();

        // 昨日までに終わらしておかないといけない「報告書作成済」のタスク
        $tasks = self::task($params['dial_id']);
        $tasks->where('app_tasks.content_id', 5);
        $tasks->where('app_tasks.task_status_id', 2);//nullを検索dd
        $report_yesterday_count = $tasks->where('app_tasks.limit_date', '<', $todayStart)->count();

        //  当日訪問　工事数
        $work_date_today_cnt = Opportunity::whereIN('status_id', [1, 2, 3, 4, 5])->where('dial_id', $params['dial_id'])->whereBetween('work_date',[$todayStart, $todayEnd])->count();
        //  当日完了案件数
        $worked_date_today_cnt = Opportunity::whereIN('status_id',[5])->where('dial_id', $params['dial_id'])->whereBetween('work_date',[$todayStart, $todayEnd])->count();
        //  当月訪問累計
        $work_date_month_cnt = Opportunity::whereIN('status_id', [1, 2, 3, 4, 5])->where('dial_id', $params['dial_id'])->whereBetween('work_date',[$startOfMonth, $todayEnd])->count();
        //  当月完了累計
        $worked_date_month_cnt = Opportunity::whereIN('status_id',[5])->where('dial_id', $params['dial_id'])->whereBetween('work_date',[$startOfMonth, $todayEnd])->count();
        //  本日売上
        $work_date_today_sum = Opportunity::whereIN('status_id',[5])->where('dial_id', $params['dial_id'])->whereBetween('worked_date',[$todayStart, $todayEnd])->selectRaw('SUM(sales) as sales_sum')->first();
        //  当月売上
        $work_date_month_sum = Opportunity::whereIN('status_id',[5])->where('dial_id', $params['dial_id'])->whereBetween('worked_date',[$startOfMonth, $todayEnd])->selectRaw('SUM(sales) as sales_sum')->first();

        //  本日粗利
        $work_date_today_opps = Oppo::whereIN('status_id',[5])->where('dial_id', $params['dial_id'])->whereBetween('work_date',[$todayStart, $todayEnd])->get();
        $work_date_today_profit_sum = 0;
        foreach($work_date_today_opps as $work_date_today_opp) {
            $work_date_today_profit = $work_date_today_opp->accountingSales();#h_getRevenueByKurapital($work_date_today_opp->receipts, $work_date_today_opp->partnerDettail, $work_date_today_opp, $work_date_today_opp->sales);
            $work_date_today_profit_sum += $work_date_today_profit;
        }
        //  当月粗利
        $work_date_month_opps = Oppo::whereIN('status_id',[5])->where('dial_id', $params['dial_id'])->whereBetween('work_date',[$startOfMonth, $todayEnd])->get();
        $work_date_month_profit_sum = 0;
        foreach($work_date_month_opps as $work_date_month_opp) {
            $work_date_month_profit = $work_date_month_opp->accountingSales(); #h_getRevenueByKurapital($work_date_month_opp->receipts, $work_date_month_opp->partnerDettail, $work_date_month_opp, $work_date_month_opp->sales);
            $work_date_month_profit_sum += $work_date_month_profit;
        }

        //  新規受付　当月累計件数
        $opportunities = Opportunity::whereIN('status_id', [1, 2, 3, 4, 5])->where('dial_id', $params['dial_id'])->whereBetween('receipt_date', [$startOfMonth, $todayEnd])->get();
        $new_cumulative = $opportunities ? count($opportunities) : 0;
        //  新規受付　当日分件数
        $opportunities = Opportunity::whereIN('status_id', [1, 2, 3, 4, 5])->where('dial_id', $params['dial_id'])->whereBetween('receipt_date', [$todayStart, $todayEnd])->get();
        $new_reception = $opportunities ? count($opportunities) : 0;

        $estimation_report_count = 0;
        $photo_report_count = 0;

        foreach($opportunities as $opportunity) {
            $estimation_report_count += $opportunity->estamationOpportunities ? count($opportunity->estamationOpportunities) : 0;
            $photo_report_count += $opportunity->opportunitiesPhotoReports ? count($opportunity->opportunitiesPhotoReports) : 0;
        }
        
        //  新規受付　一日平均
        $days = date('d');
        $daily_average = floor($new_cumulative / $days);
        //  初回調整
        $query = Opportunity::query();
        $first_adjustment = $query->whereIN('status_id', [1, 2, 3, 4, 5])->where('dial_id', $params['dial_id'])->whereBetween('receipt_date', [$startOfMonth, $todayEnd])->whereHas('tasks', function ($query) {$query->whereIn('content_id', [14]); $query->where('task_status_id', '=', 1);})->count();
        //  施工調整
        $query = Opportunity::query();
        $construction_adjustment = $query->whereIN('status_id', [1, 2, 3, 4, 5])->where('dial_id', $params['dial_id'])->whereBetween('receipt_date', [$startOfMonth, $todayEnd])->whereHas('tasks', function ($query) {$query->whereIn('content_id', [15]); $query->where('task_status_id', '=', 1);})->count();
        //  明日現場到着
        $arrival = 0;
        $arrival = Opportunity::whereIN('status_id', [1, 2, 3, 4, 5])->where('dial_id', $params['dial_id'])->where('status_id', 4)->whereBetween('work_date',[$tomorrowStart, $tomorrowEnd])->count();
        
        $result = [
            'report_total_count' => $report_total_count,
            'work_date_month_cnt' => $work_date_month_cnt,
            'report_today_count' => $report_today_count,
            'report_yesterday_count' => $report_yesterday_count,
            'work_date_today_cnt' => $work_date_today_cnt,
            'worked_date_today_cnt' => $worked_date_today_cnt,
            'worked_date_month_cnt' => $worked_date_month_cnt,
            'work_date_today_sum' => $work_date_today_sum->sales_sum,
            'work_date_month_sum' => $work_date_month_sum->sales_sum,
            'work_date_today_profit_sum' => $work_date_today_profit_sum,
            'work_date_month_profit_sum' => $work_date_month_profit_sum,
            'new_reception' => $new_reception,
            'new_cumulative' => $new_cumulative,
            'daily_average' => $daily_average,
            'photo_report_count' => $photo_report_count,
            'estimation_report_count' => $estimation_report_count,
            'first_adjustment' => $first_adjustment,
            'construction_adjustment' => $construction_adjustment,
            'arrival' => $arrival,
        ];

        return $result;
    }

    /**
     * Summary of task
     * @param mixed $params
     * @return mixed
     */
    private static function task($dial_id)
    {
        \Log::debug("DailyReportCommand::task()");
        $tasks = DB::table('app_tasks');
        $tasks->join('app_opportunity_tasks', 'app_opportunity_tasks.task_id', '=', 'app_tasks.id');
        $tasks->join('app_opportunities', 'app_opportunities.id', '=', 'app_opportunity_tasks.opportunity_id');
        $tasks->where('app_opportunities.dial_id', '=', $dial_id);

        return $tasks;
    }
}
