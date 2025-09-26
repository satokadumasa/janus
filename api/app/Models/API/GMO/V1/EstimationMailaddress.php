<?php
namespace App\Models\API\GMO\V1;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\API\GMO\V1\Kurapital\Opportunity;

class EstimationMailaddress extends Model
{
    //

    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $connection = 'gmoCrm';
    /**
     * Undocumented variable
     *
     * @var string
     */
    protected $table = 'app_estimation_mailaddresses';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    //this si the new p
    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $fillable = [
        'opportunity_id',
        'destination_name',
        'title_of_honor',
        'maikaddess',
    ];

    /**
     * Undocumented function
     *]
     * @return void
     */
    public function Opportunity()
    {
        return $this->belongsTo(Opportunity::class);
    }

    /*ß
     * Undocumented function
     *
     * @return void
     */
    public function estimationMailEstimationMailaddresses()
    {
        return $this->hasMany(EstimationMailEstimationMailaddress::class, 'id', 'estimation_mailaddress_id');
    }
}
