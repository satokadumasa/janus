<?php

namespace App\Models\API\GMO\V1;

use Illuminate\Database\Eloquent\Model;

class AccountField extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_accounts_fields';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'account_id',
        'field_id',
        'priority',
        'last_month_opportunities',
        'last_month_profit_average',
        'last_month_performance_id',
    ];
    public function fieldDetail()
    {
        return $this->hasOne(Field::class, 'id', 'field_id');
    }
}
