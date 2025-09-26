<?php

namespace App\Models\API\GMO\V1;

use Illuminate\Database\Eloquent\Model;

class AdditionalOption extends Model
{
    protected $connection = 'gmoCrm';
    protected $table = 'app_additional_options';

    protected $fillable = [
		'id',
		'name',
		'created_at',
		'updated_at',
    ];
}
