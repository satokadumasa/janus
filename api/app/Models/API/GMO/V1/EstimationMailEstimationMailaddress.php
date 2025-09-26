<?php
namespace App\Models\API\GMO\V1;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class EstimationMailEstimationMailaddress extends Model
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
    protected $table = 'app_estimation_mail_estimation_mailaddresses';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    //this si the new p
    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $fillable = [
        'estimation_mail_id',
        'estimation_mailaddress_id',
    ];

    /**
     * Undocumented function
     *]
     * @return void
     */
    public function estimationMail()
    {
        return $this->belongsTo(EstimationMail::class);
    }
    /**
     * Undocumented function
     *]
     * @return void
     */
    public function estimationMailaddress()
    {
        return $this->belongsTo(EstimationMailaddress::class);
    }
}
