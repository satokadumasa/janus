<?php

namespace App\Models\API\GMO\V1\Keiri;

use Illuminate\Database\Eloquent\Model;

class BillMemo extends Model
{
    protected $connection = 'gmoCrm';
    protected $table = 'bill_coment';
    public $timestamps = false;

    //

    public function BillMemo()
    {
        return $this->belongsTo(BillMemo::class);
    }
}
