<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\API\V1\EbisuLog;

class UpdateEbisuLogKeywordCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:updateEbisuLogKeyword';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'app_ebisu_logs.keywordにcaused_urlのパラメータ内からutm_termの値を設定する';

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
        \Log::debug("UpdateEbisuLogKeywordCommand::handle() ");
        $ebisu_logs = EbisuLog::whereNull('keyword')->get();
        foreach ($ebisu_logs as $key => $ebisu_log) {
            $params = [];
            $caused_url = urldecode($ebisu_log->caused_url);
            $params_str = explode('?', $caused_url)[1];
            $params_arr = explode('&', $params_str);
            foreach ($params_arr as $key => $value) {
                $arr = explode('=', $value);
                $params[$arr[0]] = isset($arr[1]) ? $arr[1] : '';
            }
            if(isset($params['utm_term']) && !empty($params['utm_term'])) {
                $ebisu_log->keyword = $params['utm_term'];
                $ebisu_log->save();
            }
        }

        return 0;
    }
}
