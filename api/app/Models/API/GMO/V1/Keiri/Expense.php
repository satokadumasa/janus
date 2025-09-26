<?php

namespace App\Models\API\GMO\V1\Keiri;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $connection = 'gmoCrm';
    protected $table = 'app_expenses';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';


}
