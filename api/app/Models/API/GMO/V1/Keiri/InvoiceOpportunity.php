<?php

namespace App\Models\API\GMO\V1\Keiri;

use Illuminate\Database\Eloquent\Model;

class InvoiceOpportunity extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_invoices_opportunities';
    public $timestamps = false;

    public function InvoiceOpportunity(){
        $this->belongsTo(InvoiceOpportunity::class);
    }


    public function opportunity(){
        return $this->hasOne(KeiriOpportunity::class, 'id', 'opportunity_id');
    }

    public function ReceiptPlan(){
        return $this->hasOne(ReceiptPlan::class,'opportunity_id', 'opportunity_id');
    }

}
