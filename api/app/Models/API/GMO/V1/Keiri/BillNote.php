<?php

namespace App\Models\API\GMO\V1\Keiri;

use App\Models\API\GMO\V1\bill;
use Illuminate\Database\Eloquent\Model;

use App\Models\API\GMO\V1\user;

class BillNote extends Model
{
    protected $table = 'app_bill_notes';
    protected $fillable = [
        'user_id',
        'bill_id',
        'note',
        'disabled'
    ];

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function bill()
    {
        return $this->belongsTo(bill::class, 'bill_id');
    }
    public function UserDetail()
    {
        return $this->hasOne(user::class, 'id', 'user_id');
    }
}
