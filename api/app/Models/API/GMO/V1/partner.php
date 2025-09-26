<?php

namespace App\Models\API\GMO\V1;

use App\Models\API\GMO\V1\Partner\PartnerTimemangement;
use App\Models\API\GMO\V1\Partner\Schedule;
use App\Models\API\GMO\V1\account;
use Carbon\Carbon;
// use Illuminate\Foundation\Auth\user as Authenticatable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class partner extends Authenticatable //Model

{
    use HasApiTokens, Notifiable;
    protected $guard = 'partner';
    protected $connection = 'gmoCrm';
    protected $table = 'app_accounts';
    protected $hidden = [
        'password', 'remember_token',
    ];

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
    public $timestamps = false;
    protected $fillable = [
        'store_name',
        'email1',
        'password',
        "id",
        "company_name",
        "postal_code",
        "prefecture",
        "city",
        "address",
        "representative_familyname",
        "representative_firstname",
        "representative_familyname_kana",
        "representative_firstname_kana",
        "representative_position",
        "skill",
        "area",
        "business_hours",
        "phone1",
        "phone2",
        "phone3",
        "fax",
        "email2",
        "email3",
        "opportunity_email1",
        "opportunity_email2",
        "opportunity_email3",
        "url",
        "share",
        "note",
        "transfer_account",
        "transfer_fee",
        "share_method_id",
        "parent_company_id",
        "business_hour_from",
        "business_hour_to",
        "holidays",
        "latitude",
        "longitude",
        "score",
        "react_scope1",
        "react_scope2",
        "ext1",
        "ext2",
        "ext3",
        "react_scope3",
        "billing_email1",
        "billing_email2",
        "billing_email3",
        "disabled",
        "contract_company_id",
        "memo",
        "payer_name",
        "exclude_payer_name",
        "show_schedule",
        "check_daily_sales",
        "check_daily_payment",
        "chatwork_id",
        "last_month_opportunities",
        "last_month_profit_average",
        "last_month_performance_id",
        "shoukai",
        "kaitori",
        "senzoku",
        "p_sortid",
        "schedule_color",
        "lastLogin",
        'is_ac_designated',
        'is_insect_designated',
        'login_email',
        'is_hojin_designated',
        'is_capital_area_designated',
        'is_suburbs_designated'
    ];
    public function partner()
    {
        return $this->belongsTo(partner::class);
    }
    public function children()
    {
        return $this->hasMany(partner::class, 'parent_company_id', 'id');
    }

    public function bills()
    {
        return $this->hasMany(bill::class, 'account_id', 'id');
    }

    public function PartnerTimeLatest()
    {
        return $this->hasMany(PartnerTimemangement::class, 'account_id', 'id')->orderBy('created_at', 'DESC')->limit(1);
    }
    public function getTodayRegisterScheduleAttribute()
    {
        $toaySchedule = Schedule::where('partner_id', $this->id)
            ->where('year', Carbon::now()->format('Y'))
            ->where('month', Carbon::now()->format('m'))
            ->where('day', Carbon::now()->format('d'))
            ->get();
        return $toaySchedule;
    }
    public function getTodayTimeManagementAttribute()
    {
        if ($this->id) {
            $now_data = Carbon::now()->format('Y-m-d') . ' 00:00:00';
            $account_id = $this->id;
            $query = PartnerTimemangement::query();
            $query->where('account_id', $account_id)
                ->where('created_at', '>', $now_data)
                ->orderBy('created_at', 'DESC')->first();
            return $query->get();
        }
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function opportunities()
    {
        return $this->hasMany(opportunity::class, 'account_id');
    }
    public function accountFields()
    {
        return $this->hasMany(AccountField::class, 'account_id');
    }
    public function accountWorkTarget()
    {
        return $this->hasMany(AccountWorkTarget::class, 'account_id');
    }
    public function accountPrefecture()
    {
        return $this->hasMany(AccountPrefecture::class, 'account_id');
    }
    public function accountSubcategoryDetail()
    {
        return $this->hasMany(AccountSubCategory::class, 'account_id');
    }

    public function getFieldsIdsAttribute()
    {
        // var_dump($this->accountFields);exit;
        if (!empty($this->accountFields)) {
            $accountFields = $this->accountFields;
            $account_fields = [];

            foreach ($accountFields as $field) {
                $account_fields[] = strval($field->field_id);
            }

            return $account_fields;
        }
        return null;
    }
    public function getWorksTargetIdsAttribute()
    {
        // var_dump($this->accountWorkTarget);exit;
        if (!empty($this->accountWorkTarget)) {
            $accountWorkTargets = $this->accountWorkTarget;
            $accountWorkTarget = [];

            foreach ($accountWorkTargets as $target) {
                $accountWorkTarget[] = strval($target->work_target_id);
            }

            return $accountWorkTarget;
        }
        return null;
    }

    public function getPrefectureIdsAttribute()
    {
        // var_dump($this->accountWorkTarget);exit;
        if (!empty($this->accountPrefecture)) {
            $accountPrefectures = $this->accountPrefecture;
            $accountPrefecture = [];

            foreach ($accountPrefectures as $target) {
                $accountPrefecture[] = strval($target->prefecture_id);
            }

            return $accountPrefecture;
        }
        return null;
    }
    public function getAccountSubcategoryAttribute()
    {
        // var_dump($this->accountWorkTarget);exit;
        if (!empty($this->accountSubcategoryDetail)) {
            $accountSubcategoryDetails = $this->accountSubcategoryDetail;
            $accountaccountSubcate = [];

            foreach ($accountSubcategoryDetails as $subcate) {
                $accountaccountSubcate[] = ($subcate->sub_category_id);
            }

            return $accountaccountSubcate;
        }
        return null;
    }
    public function getBusinessStartTimeAttribute()
    {

        if (!empty($this->business_hour_from)) {
            $datetime = $this->business_hour_from;
            $format = (explode(":", $datetime));
            $hour = $format[0];
            $minute = $format[1];

            return [
                'hour' => $hour,
                'minute' => $minute,
            ];
        }
        return [
            'hour' => '00',
            'minute' => '00',
        ];
    }
    public function getBusinessEndTimeAttribute()
    {

        if (!empty($this->business_hour_to)) {
            $datetime = $this->business_hour_to;
            $format = (explode(":", $datetime));
            $hour = $format[0];
            $minute = $format[1];

            return [
                'hour' => $hour,
                'minute' => $minute,
            ];
        }
        return [
            'hour' => '00',
            'minute' => '00',
        ];
    }
    /**
     * Undocumented function
     * アカウントIDに紐づく子アカウントデータを取得
     * @return void
     */
    public function getChildCompanyDataAttribute()
    {
        // 自分のaccount情報を取得
        $data = account::query()->where('id', $this->id)->get();
        foreach ($data as $val) {
            $ko_aka_data[$val->id] = $val;
            // disabledを強制的に0にする
            $ko_aka_data[$val->id]->disabled = 0;
        }
        // アカウントテーブルから子アカウントのIDを取得
        $data = account::query()->where('parent_company_id', $this->id)->get();
        foreach ($data as $val) {
            $ko_aka_data[$val->id] = $val;
            // disabledを強制的に0にする
            $ko_aka_data[$val->id]->disabled = 0;
        }
        // 取得した該当userIDを返却
        return $ko_aka_data;
    }
    /**
     * Undocumented function
     * アカウントIDに紐づく子アカウントIDを取得
     * @return void
     */
    public function getChildCompanyIdsAttribute()
    {
        // アカウントテーブルから子アカウントのIDを取得
        $ids = account::query()->where('parent_company_id', $this->id)->pluck('id');
        //user_idを配列に追加
        $ko_aka_ids[] = $this->id;
        foreach ($ids as $val) {
            $ko_aka_ids[] = $val;
        }
        // 取得した該当userIDを返却
        return $ko_aka_ids;
    }
}
