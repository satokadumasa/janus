<?php
namespace App\Models\API\GMO\V1;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\API\GMO\V1\Kurapital\Opportunity;

class EstimationMail extends Model
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
    protected $table = 'app_estimation_mails';
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
        'estimation_id',
        'title',
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
    /**
     * Undocumented function
     *]
     * @return void
     */
    public function Estimation()
    {
        return $this->belongsTo(Estimation::class);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function estimationMailEstimationMailaddresses()
    {
        return $this->hasMany(EstimationMailEstimationMailaddress::class, 'estimation_mail_id');
    }
}
