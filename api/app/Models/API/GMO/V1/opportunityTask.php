<?php

namespace App\Models\API\GMO\V1;

use Illuminate\Database\Eloquent\Model;

class opportunityTask extends Model
{
    //
    protected $connection ='gmoCrm';

    protected $table = 'app_opportunity_tasks';
}
