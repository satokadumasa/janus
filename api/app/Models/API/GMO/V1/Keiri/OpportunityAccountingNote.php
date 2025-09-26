<?php

namespace App\Models\API\GMO\V1\Keiri;

use Illuminate\Database\Eloquent\Model;

class OpportunityAccountingNote extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table ='app_opportunity_accounting_notes';

    public function OpportunityAccountingNote(){
        return $this->belongsTo(OpportunityAccountingNote::class);
    }

}
