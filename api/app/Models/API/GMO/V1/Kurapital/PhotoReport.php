<?php

namespace App\Models\API\GMO\V1\Kurapital;

use App\Models\API\GMO\V1\user;
use Illuminate\Database\Eloquent\Model;
use App\Models\API\GMO\V1\Kurapital\OpportunityPhotoReport;
class PhotoReport extends Model
{
    //
 protected $connection = 'gmoCrm';
    protected $table ='app_photo_reports';
    // public $timestamps = false;
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        'id',
        'issue_date',
        'title',
        'company_name',
        'start_date',
        'end_date',
        'address',
        'image_title_1',
        'image_title_2',
        'image_title_3',
        'image_title_4',
        'image_title_5',
        'image_title_6',
        'details',
        'hash',
        'items',
        'with_tax',
        'amount',
        'user_id',
        'created',
        'modified',
        'items',
        'with_tax',
        'amount',
        'image_files',
        'sent_date',
        'trace_no',
        'tax_rate',
    ];

    /**
     * Undocumented function
     *
     * @return void
     */
    public function invoice (){
        return $this->belongsTo(PhotoReport::class);
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

    public function opportunitiesPhotoReport()
    {
        return $this->hasMany(OpportunityPhotoReport::class);
    }
}
