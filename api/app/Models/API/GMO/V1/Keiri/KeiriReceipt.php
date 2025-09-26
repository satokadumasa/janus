<?php

namespace App\Models\API\GMO\V1\Keiri;

use Illuminate\Database\Eloquent\Model;

class KeiriReceipt extends Model
{
    //
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
    public function opportunity()
    {
        return $this->hasOne(KeiriOpportunity::class, 'id', 'opportunity_id');
    }
    public function accountDetail(){
        return $this->belongsTo(KeiriAccount::class, 'account_id', 'id');
    }

    // public function opportunitiy(){
    //     return $this->hasOne(KeiriOpportunity::class,'id', 'opportunity_id');
    // }

    public function getOpportunitIdAttribute(){
        $returnId = '';
        if($this->opportunity){
            if($this->opportunity->fieldDetail){
                $fieldsDetail = $this->opportunity->fieldDetail;
                $opportunity = $this->opportunity;
        
                $opportunity_id = h_getFormattedAnkenId($opportunity->id,$fieldsDetail->alias);
                $returnId = $opportunity_id;
            }
        }
        return $returnId;
        
    }

    public  function getRevenue($input)
    {
        $account = KeiriAccount::find($this->account_id);
        $opportunity = KeiriOpportunity::find($this->opportunity_id);
        $amount = $this->amount;
        $share_method_id = $account->share_method_id;
        $share = $account->share;
        $other_cost = $opportunity->other_cost;
        $material_cost = $opportunity->material_cost;
        $revenue = 0;
        if ($share_method_id == 1) {
            $revenue = ($amount - $material_cost - $other_cost) * (1.0 - $share);
        } else if ($share_method_id == 2) {
            $revenue = ($amount - $other_cost) * (1.0 - $share);
        } else {
            $kurapital = ($amount - $other_cost) * (1.0 - $share);
            $partner = ($amount - $other_cost) * $share - $material_cost;
            if ($kurapital <= $partner) {
                $revenue = ($amount - $other_cost) * (1.0 - $share);
            } else {

                $sales2 = $amount - $other_cost;
                $cost = ($sales2 + $material_cost - 2 * $share * $sales2) / (2 - 2 * $share);
                $revenue = ($amount - $cost - $other_cost) * (1.0 - $share);
            }
        }
        return floor($revenue);
    }
}
