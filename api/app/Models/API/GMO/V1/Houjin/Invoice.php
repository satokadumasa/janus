<?php

namespace App\Models\API\GMO\V1\Houjin;

use App\Models\API\V1\OtherBillDetail;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'app_invoices';
    protected $fillable = [
        'template_no',
        'trace_no',
        'create_user_id',
        'user_id',
        'bill_date',
        'due_date',
        'sent_date',
        'postcode',
        'address',
        'name',
        'details',
        'with_tax',
        'amount',
        'enabled',
        'note',
        'collected_flg'
    ];

    public $timestamps = false;

    public function otherBillDetail()
    {
        return $this->hasMany(OtherBillDetail::class, 'invoice_id', 'id');
    }
}
