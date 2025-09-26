<?php

namespace App\Models\API\GMO\V1;

use Illuminate\Database\Eloquent\Model;

class KensakuOppo extends Model
{
    //
    protected $connection ='gmoCrm';
    protected $table ='app_kensaku_opps';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'tel',
        'opportunity_id',
    ];
    public function KensakuOppo()
    {
        return $this->belongsTo(KensakuOppo::class);
    }
    public function opportunity()
    {
        return $this->hasOne(opportunity::class,'id', 'opportunity_id');
    }
}
