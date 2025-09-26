<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        Commands\API\V1\OpportunityGeoCodeSchedule::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('opportunity:geocode')
            ->everyMinute();

        $schedule->command('create:dailybills')
            ->dailyAt('8:00');

        //未更新催促メールを一時的に取りやめにする
        // $schedule->command('send:remindmails')
        //     ->dailyAt('8:00');

        $schedule->command('create:techsBills')->dailyAt('8:05');

		// ebisuの評価テーブルの作成以降にしてください（2024/2/22現在　0:30に稼働
        $schedule->command('command:replicate_ebisu_callLog')->dailyAt('1:30');

        $schedule->command('create:callLogs')->dailyAt('2:00');
        
        $schedule->command('create:offlineCvDatas')->dailyAt('2:30');

        $schedule->command('command:SyncAdsAllDatasCommand')->dailyAt('4:00');

		$schedule->command('command:SendUnPaidOpportunitiesCommand')->monthlyOn(2, '7:00');

        //  法人日報
        // $schedule->command('create:daily_report')->dailyAt('1:00');
    }
    
    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
