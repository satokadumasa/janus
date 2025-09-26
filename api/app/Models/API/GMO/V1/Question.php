<?php

namespace App\Models\API\GMO\V1;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $connection = 'gmoCrm';
    protected $table = 'app_questions';

    protected $fillable = [
        'id',
        'number',
        'body',
        'allocation_of_points',
        'start_using_date',
        'created_at',
        'updated_at',
    ];
}
