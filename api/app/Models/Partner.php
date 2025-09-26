<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Partner extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    /**
     * テーブルネーム
     * @var string
     */
    // protected $table = 'app_users';
    protected $table = 'partners';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'hash',
        'login_email',
        'store_name',
        'company_name',
        'postal_code',
        'prefecture',
        'city',
        'address',
        'representative_familyname',
        'representative_firstname',
        'representative_familyname_kana',
        'representative_firstname_kana',
        'representative_position',
        'skill',
        'area',
        'business_hours',
        'phones',
        'fax',
        'url',
        'share',
        'transfer_account',
        'transfer_fee',
        'share_method_id',
        'parent_company_id',
        'business_hour_from',
        'business_hour_to',
        'holidays',
        'latitude',
        'longitude',
        'score',
        'react_scopes',
        'ext1',
        'ext2',
        'ext3',
        'contract_company_id',
        'payer_name',
        'exclude_payer_name',
        'show_schedule',
        'check_daily_sales',
        'check_daily_payment',
        'chatwork_id',
        'last_month_opportunities',
        'last_month_profit_average',
        'last_month_performance_id',
        'shoukai',
        'kaitori',
        'senzoku',
        'p_sortid',
        'schedule_color',
        'password',
        'lastLogin',
        'is_ars_employee',
        'is_ac_designated',
        'is_insect_designated',
        'is_hojin_designated',
        'is_capital_area_designated',
        'is_suburbs_designated',
        'is_only_aircon',
        'hojin_p_sortid',
        'memo',
        'note',
        'disabled',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    public function getAvatarAttribute($value)
    {
        return $value ? url('storage/' . $value) : null;
    }
}
