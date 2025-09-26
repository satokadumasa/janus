<?php

namespace App\Models\API\GMO\V1;

use App\Models\API\V1\user;
use Illuminate\Database\Eloquent\Model;

class opportunityNote extends Model
{
    //
 protected $connection = 'gmoCrm';
    protected $table = 'app_opportunity_notes';
    public $timestamps = false;

    protected $fillable = [
        // 'id',
        'user_id',
        'opportunity_id',
        'note',
        'disabled',
        'created',
        'modified'
    ];
    public function UserDetails(){
        return $this->hasOne(user::class ,'id','user_id');
    }
}
