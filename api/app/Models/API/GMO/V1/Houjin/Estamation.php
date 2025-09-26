<?php

namespace App\Models\API\GMO\V1\Houjin;

use App\Models\API\GMO\V1\user;
use Illuminate\Database\Eloquent\Model;

class Estamation extends Model
{
    //
    protected $connection = 'gmoCrm';
    // protected $connection = 'gmoCrm';
    protected $table = 'app_estimations';
    public $timestamps = false;
    // const CREATED_AT = 'created';
    // const UPDATED_AT = 'modified';
    protected $fillable = [
        'id',
        'template_no',
        'trace_no',
        'user_id',
        'create_user_id',
        'create_date',
        'sent_date',
        'postcode',
        'address',
        'name',
        'details',
        'with_tax',
        'amount',
        'enabled',
        'note',
        'display_flag',
        'hash',
        'tax_rate',
        'is_correct',
    ];
    public function estamation()
    {
        return $this->belongsTo(Estamation::class);
    }
    public function userDetail()
    {
        return $this->hasOne(user::class, 'id', 'user_id');
    }
    public function createUserDetail()
    {
        return $this->hasOne(user::class, 'id', 'create_user_id');
    }
}
