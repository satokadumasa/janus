<?php

namespace App\Models\API\GMO\V1\Kurapital\Houjin;

use App\Models\API\GMO\V1\Kurapital\PhotoReport;
use Illuminate\Database\Eloquent\Model;

class PhotoReportOpportunity extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_invoices_opportunities';
    public $timestamps = false;

    public function photoReport(){
        return $this->hasMany(PhotoReport::class, 'id','estimation_id');
    }
}
