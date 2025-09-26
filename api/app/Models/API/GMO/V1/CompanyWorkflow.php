<?php

namespace App\Models\API\GMO\V1;

use Illuminate\Database\Eloquent\Model;

class CompanyWorkflow extends Model
{
    protected $table = 'app_company_workflows';
    protected $fillable = [
		'id',
		'company_id',
		'title',
		'content',
		'description',
		'created_at',
		'updated_at',
    ];
}
