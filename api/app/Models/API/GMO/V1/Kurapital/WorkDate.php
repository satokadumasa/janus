<?php
namespace App\Models\API\GMO\V1\Kurapital;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\API\V1\PartnerSchedule;
use Illuminate\Database\Eloquent\Model;
use App\Models\API\GMO\V1\Partner\Schedule;
use App\Models\API\GMO\V1\Partner\PartnerTimemangement;
use App\Http\Resources\API\V1\Kurapital\OpportunityResource;
use App\Models\API\V1\user;

class WorkDate extends Model
{
    //
    protected $connection = 'gmoCrm';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $table = 'app_work_dates';
    protected $fillable = [
        'opportunity_id',
        'work_date',
    ];
    private $account_id;

    /**
     * Undocumented function
     *
     * @return void
     */
    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class, 'account_id');
    }
}
