<?php

namespace App\Models\API\GMO\V1\Kurapital;

use App\Http\Resources\API\GMO\V1\Kurapital\EstamationResource;
use Illuminate\Database\Eloquent\Model;
use App\Models\API\GMO\V1\Kurapital\Opportunity;

class EstamationOpportunity extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_estimations_opportunities';
    public $timestamps = false;
    protected $fillable = [
        'estimation_id',
        'opportunity_id',
    ];


    public function estamationOpportunity()
    {
        return $this->belongsTo(EstamationOpportunity::class);
    }
    public function estamation()
    {
        return $this->hasOne(Estamation::class,'id', 'estimation_id');
    }
    public function opportunity()
    {
        return $this->hasOne(Opportunity::class,'id', 'opportunity_id');
    }
}
