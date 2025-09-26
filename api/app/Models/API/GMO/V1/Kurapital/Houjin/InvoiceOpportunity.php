<?php

namespace App\Models\API\GMO\V1\Kurapital\Houjin;

use App\Models\API\GMO\V1\Kurapital\Invoice;
use Illuminate\Database\Eloquent\Model;

class InvoiceOpportunity extends Model
{
    //

    protected $connection = 'gmoCrm';
    protected $table = 'app_invoices_opportunities';
    public $timestamps = false;

    public function estimation(){
        return $this->hasMany(Invoice::class, 'id','estimation_id');
    }

}
