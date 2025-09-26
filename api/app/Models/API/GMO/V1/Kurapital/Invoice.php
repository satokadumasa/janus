<?php

namespace App\Models\API\GMO\V1\Kurapital;

use App\Models\API\GMO\V1\user;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_invoices';
    // public $timestamps = false;
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
    /**
     * Undocumented function
     *
     * @return void
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
    /**
     * Undocumented function
     *
     * @return void
     */
    public function userDetail()
    {
        return $this->hasOne(user::class, 'id', 'user_id');
    }

    public function createUserDetail()
    {
        return $this->hasOne(user::class, 'id', 'create_user_id');
    }
}
