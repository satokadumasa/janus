<?php

namespace App\Models\API\GMO\V1\Kurapital;

use Illuminate\Database\Eloquent\Model;

class OpportunityTask extends Model
{
    protected $connection = 'gmoCrm';
    protected $table = 'app_opportunity_tasks';

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        'note',
        'opportunity_id',
        'task_id',
    ];
}
