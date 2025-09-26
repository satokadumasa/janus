<?php

namespace App\Models\API\GMO\V1\Partner;

use Illuminate\Database\Eloquent\Model;
use App\Models\API\GMO\V1\Partner\PartnerTimemangementType;
use Carbon\Carbon;

class PartnerTimemangement extends Model
{
    protected $connection = 'gmoCrm';
    protected $table = 'app_time_managements';
    // const CREATED_AT = 'start_time';
    // const UPDATED_AT = 'modified';

    protected $fillable = [
        'account_id',
        'opportunity_id',
        'type_id',
        'start_time',
        'end_time',
        'group_id',
        'start_lat',
        'start_long',
        'start_prefecture',
        'start_city',
        'start_street',
        'start_other',
        'start_full_address',
        'end_lat',
        'end_long',
        'end_prefecture',
        'end_city',
        'end_street',
        'end_other',
        'end_full_address',

    ];
    public function typeDetail()
    {
        return $this->hasOne(PartnerTimemangementType::class, 'id', 'type_id');
    }

    public function partner()
    {
        return $this->hasOne(Account::class, 'id', 'account_id');
    }
    public function getWhichButtonToshowAttribute()
    {
        // return $this->partner->hash;
        $todaySchedule = $this->partner->TodaySchedule;

        if (!empty($todaySchedule)) {
            $datetime = $todaySchedule->year . '-' . $todaySchedule->month . '-' . $todaySchedule->day;
        }
        if ($datetime == '--') {
            $datetime = Carbon::now()->format('Y-m-d');
        }
        $datetime = Carbon::parse($datetime)->format('Y-m-d');
        // return $datetime;
        $noschedule = true;//今日のスケジュールない
        $opportunity_start_button = true; //案件開始
        $opportunity_end_button = true; //案件完了
        $waiting_start_button = true; //\待機開始
        $waiting_end_button = true; //待機完了
        $start_time = $this->start_time;
        $end_time = $this->end_time;
        // $account_id = $this->account_id;
        $opportunity_id = ($this->opportunity_id) ? $this->opportunity_id : '';
        $created = Carbon::parse($this->created_at)->format('Y-m-d H:i:s');
        $created_day = Carbon::parse($this->created_at)->format('Y-m-d');
        $current_date_morning = Carbon::now()->format('Y-m-d') . ' 23:59:59';
        $today = Carbon::now()->format('Y-m-d');

        //$todaySchedule->forsort
        if ($datetime == $today) {
            // return 'i am here';
            $noschedule = false;
            if ($created < $current_date_morning) {
                if ($today == $created_day) {
                    if (!$opportunity_id) {
                        if ($start_time && !$end_time) {
                            $waiting_end_button = false;//display on
                        }

                        if ($start_time && $end_time) {
                            $waiting_start_button = false;
                            $opportunity_start_button = false;
                        }
                    }
                    if ($opportunity_id) {
                        if ($start_time && !$end_time) {
                            $opportunity_end_button = false;
                        }
                        if ($start_time && $end_time) {
                            $opportunity_start_button = false;
                            $waiting_start_button = false;
                        }
                    }
                    if ($start_time && $end_time) {
                        $opportunity_start_button = false;
                    }
                }
                if ($today != $created_day) {
                    $waiting_start_button = false;
                    $opportunity_start_button = false;
                }
            }
        }


        $return_data = [
            'waiting_start_button' => $waiting_start_button,
            'waiting_end_button' => $waiting_end_button,
            'opportunity_start_button' => $opportunity_start_button,
            'opportunity_end_button' => $opportunity_end_button,
            'opportunity_id' => $opportunity_id,
            'noSchedule' => $noschedule,
            'partnerHash' => $this->partner->hash

        ];
        return $return_data;
    }
    public function getTodayOpportunityCountAttribute()
    {
        $account_id = $this->account_id;
        $opportunityCount = Opportunity::where('account_id', $account_id);
        return $account_id;
    }
}
