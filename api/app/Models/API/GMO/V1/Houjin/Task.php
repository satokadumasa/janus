<?php

namespace App\Models\API\GMO\V1\Houjin;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    //
    protected $connection = 'gmoCrm';
    protected $table = 'app_tasks';

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

}
