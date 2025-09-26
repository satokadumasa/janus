<?php

namespace App\Models\API\GMO\V1\Kurapital\Houjin;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_invoices';
    public $timestamps = false;


    public function Invoice(){
        return $this->belongsTo(Invoice::class);
    }

}
