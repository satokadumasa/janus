<?php

namespace App\Models\API\GMO\V1\Partner;

use Illuminate\Database\Eloquent\Model;

class PartnerTimemangementType extends Model
{
    protected $connection = 'gmoCrm';
    protected $table ='app_time_management_types';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        'type_name',
        'group_id',
    ];
}
