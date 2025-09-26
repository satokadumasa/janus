<?php

namespace App\Models\API\GMO\V1\Kurapital\Houjin;

use Illuminate\Database\Eloquent\Model;

class DfcInquiryInformationsOpportunity extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_dfc_inquiry_informations_opportunities';
    public $timestamps = false;

    /**
     * Undocumented function
     *
     * @return void
     */
    public function DfcInquiryInformationsOpportunity(){
        return $this->belongsTo(DfcInquiryInformationsOpportunity::class);
    }
}
