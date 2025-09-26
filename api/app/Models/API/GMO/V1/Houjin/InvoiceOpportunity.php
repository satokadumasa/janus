<?php

namespace App\Models\API\GMO\V1\Houjin;

use Illuminate\Database\Eloquent\Model;

class InvoiceOpportunity extends Model
{
    protected $connection = 'gmoCrm';
    protected $table = 'app_invoices_opportunities';
    public $timestamps = false;

    //relation
    public function Invoice()
    {
        return $this->hasOne(Invoice::class, 'id', 'invoice_id');
    }
}
