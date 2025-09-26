<?php

namespace App\Models\API\GMO\V1\Kurapital;

use App\Models\API\GMO\V1\user;
use Illuminate\Database\Eloquent\Model;

class OpportunityPhotoReport extends Model
{
    //
 protected $connection = 'gmoCrm';
    protected $table ='app_opportunities_photo_reports';
    const CREATED_AT = null;
    const UPDATED_AT = null;
    protected $fillable = [
        'photo_report_id',
        'opportunity_id',
    ];
    // public $timestamps = false;
    /**
     * Undocumented function
     *
     * @return void
     */
    public function opportunity (){
        return $this->belongsTo(Opportunity::class);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function photo_report (){
        return $this->belongsTo(PhotoReport::class);
    }
}
