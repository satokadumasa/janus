<?php

namespace App\Models\API\GMO\V1;

use Illuminate\Database\Eloquent\Model;
use App\Models\API\GMO\V1\AdditionalOption;
use App\Models\API\GMO\V1\CustomerQuestionnaire;

class CustomerQuestionnaireAdditionalOpption extends Model
{
    protected $connection = 'gmoCrm';
    protected $table = 'app_customer_questionnaire_additional_opptions';

    protected $fillable = [
		'id',
		'customer_questionnaire_id',
		'additional_option_id',
		'created_at',
		'updated_at',
    ];

    public function additionalOption()
    {
        return $this->hasOne(AdditionalOption::class, 'id', 'additional_option_id');
    }

    public function customerQuestionnaire()
    {
        return $this->belongsTo(CustomerQuestionnaire::class, 'id', 'customer_questionnaire_id');
    }

}
