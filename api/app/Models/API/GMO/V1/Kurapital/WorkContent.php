<?php

namespace App\Models\API\GMO\V1\Kurapital;

use Illuminate\Database\Eloquent\Model;

class WorkContent extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_work_contents';

    /**
     * Undocumented function
     *
     * @return void
     */
    function WorkContent(){
        return $this->belongsTo(WorkContent::class);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function Opportunity(){
        return $this->hasMany(Opportunity::class,'work_content_id', 'id');
    }

}
