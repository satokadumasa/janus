<?php

namespace App\Models\API\GMO\V1\Partner;

use Carbon\Carbon;
use App\Models\API\GMO\V1\bill;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{

    protected $connection = 'gmoCrm';
    protected $table = 'app_accounts';
    /**
     * Undocumented function
     *
     * @return void
     */
    public function bill()
    {
        return $this->hasMany(bill::class);
    }

    public function Schedule()
    {
        return $this->hasMany(Schedule::class, 'partner_id', 'id');
    }


    public function TimeManagement()
    {
        return $this->hasMany(PartnerTimemangement::class, 'account_id', 'id');
    }

    public function getTodayTimeManagementAttribute()
    {
        $id = $this->id;
        $todayStatus = PartnerTimemangement::query()
            ->whereBetween('created_at', [Carbon::now()->format('Y-m-d') . ' 00:00:00', Carbon::now()->format('Y-m-d') . ' 23:59:59'])
            ->whereNull('end_time')
            ->where('account_id', $id)
            ->get();
        if (count($todayStatus) > 0) {
            return $todayStatus[0];
        } else {
            $todayStatus = PartnerTimemangement::query()
            ->whereBetween('created_at', [Carbon::now()->format('Y-m-d') . ' 00:00:00', Carbon::now()->format('Y-m-d') . ' 23:59:59'])
            ->where('end_time','<>',null)
            ->where('account_id', $id)
            ->orderBy('updated_at','DESC')
            ->limit(1)
            ->get();
            $data = $todayStatus;
            if(count($data) == 1){
                $todaySchedule = $todayStatus[0];
                $returnValue = [
                    'id' => $todaySchedule['id'],
                    'account_id' => $todaySchedule['account_id'],
                    'opportunity_id' => 0,//$todaySchedule['opportunity_id'],
                    'type_id' => 2,//$todaySchedule['type_id'],
                    'start_time' => $todaySchedule['start_time'],
                    'end_time' => $todaySchedule['end_time'],
                    'group_id' => $todaySchedule['group_id'],
                    'start_lat' => $todaySchedule['start_lat'],
                    'start_long' => $todaySchedule['start_long'],
                    'start_prefecture' => $todaySchedule['start_prefecture'],
                    'start_city' => $todaySchedule['start_city'],
                    'start_street' => $todaySchedule['start_street'],
                    'start_other' => $todaySchedule['start_other'],
                    'start_full_address' => $todaySchedule['start_full_address'],
                    'end_lat' => $todaySchedule['end_lat'],
                    'end_long' => $todaySchedule['end_long'],
                    'end_prefecture' => $todaySchedule['end_prefecture'],
                    'end_city' => $todaySchedule['end_city'],
                    'end_street' => $todaySchedule['end_street'],
                    'end_other' => $todaySchedule['end_other'],
                    'end_full_address' => $todaySchedule['end_full_address'],
                    'created_at' => $todaySchedule['created_at'],
                    'updated_at' => $todaySchedule['updated_at'],
                ];
                // $todayStatus = (object) $todayStatus;
                return $returnValue;
            }else{
                $data = [
                    'id' => 0,
                    'account_id' => 0,
                    'opportunity_id' => 0,
                    'type_id' => null,
                    'start_time' => Carbon::now()->format('Y-m-d h:is'),
                    'end_time' => Carbon::now()->format('Y-m-d h:is'),
                    'group_id' => 1,
                    'start_lat' => null,
                    'start_long' => null,
                    'start_prefecture' => '未設定',
                    'start_city' => '未設定',
                    'start_street' => '未設定',
                    'start_other' => '未設定',
                    'start_full_address' => '未設定',
                    'end_lat' => '未設定',
                    'end_long' => '未設定',
                    'end_prefecture' => '未設定',
                    'end_city' => '未設定',
                    'end_street' => '未設定',
                    'end_other' => '未設定',
                    'end_full_address' => '未設定',
                    'created_at' => Carbon::now()->format('Y-m-d h:is'),
                    'updated_at' => Carbon::now()->format('Y-m-d h:is'),
                ];
                return $data;
            }


            // */

        }
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getCountTodaysOpportunityAttribute()
    {
        $id = $this->id;
        $today_start = Carbon::now()->format('Y-m-d') . ' 00:00:00';
        $today_end = Carbon::now()->format('Y-m-d') . ' 23:59:59';
        $status_id = 4;
        $todayTotal = 0;
        $todayCompleted = 0;
        $todayOppo = Opportunity::query()
            ->where('account_id', $id)
            ->where('status_id', $status_id)
            ->whereBetween('work_date', [$today_start, $today_end])
            ->get();

        if (count($todayOppo) > 0) {
            foreach ($todayOppo as $today) {
                $today = (object) $today;
                if ($today->is_completed == 1) {
                    $todayCompleted++;
                }
                // if($today->is_completed == 0 && $today->status_id == 6) {
                //     $todayCompleted++;
                // }
            }
        }
        $remaining =   count($todayOppo) - ($todayCompleted);

        $adjoins = Adjoin::where('account_id',$id)
                        ->whereBetween('work_date', [$today_start, $today_end])
                        ->count();

        $returnValue = [
            'todayTotal' => count($todayOppo),
            'complete' => $todayCompleted,
            'remaining' => $remaining,
            'adjoins' => $adjoins
        ];
        return $returnValue;
    }


    public function getTodayScheduleAttribute()
    {
        $id = $this->id;
        $scheduleData = Schedule::query()
            ->where('partner_id', $id)
            ->where('year',Carbon::now()->format('Y'))
            ->where('month', Carbon::now()->format('m'))
            ->where('day',Carbon::now()->format('d'))
            ->get();
        $returnValue = [];
        if (count($scheduleData) > 0) {
            foreach ($scheduleData as $today) {
                $sub_array = [];
                $today = (object) $today;
                $today['is_register'] = true;
                if ($today->open != '休み') {
                    $today['is_working'] = true;
                    //$todayCompleted++;
                } else {
                    $today['is_working'] = false;
                }
                $returnValue[] = $today;
            }
            $returnValue = $returnValue[0];
        } else {
            $data = [
                'id' => null,
                'partner_id' => null,
                'partner_account_hash' => null,
                'year' => null,
                'month' => null,
                'day' => null,
                'open' => null,
                'close' => null,
                'partner_name' => null,
                'note' => null,
                'week_day' => null,
                'created' => null,
                'forsort' => null,
                'sortid' => null,
                'is_holiday' => null,
                'is_register' => false,
                'is_working' => false,
            ];
            // $returnValue['is_register'] = false;
            $returnValue = $data;
        }
        $returnValue = (object) $returnValue;

        return $returnValue;
    }
}
