<?php

namespace App\Models\API\GMO\V1\Kurapital;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $connection = 'gmoCrm';
    protected $table ='app_receipts';
    // public $timestamps = false;
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
    protected $fillable = [
        'id',
        'account_id',
        'payment_method_id',
        'opportunity_id',
        'bill_detail_id',
        'bank_id',
        'name',
        'amount',
        'received',
        'irrecoverable',
        'by_kurapital',
        'note',
        'due_date',
        'receipt_date',
        'created',
        'modified',
    ];
    // public function opportunity()
    // {
    //     return $this->belongsTo(opportunity::class, 'opportunity_id', 'id');
    // }
    // public function accountDetail(){
    //     return $this->belongsTo(Account::class, 'opportunity_id', 'id');
    // }
}
