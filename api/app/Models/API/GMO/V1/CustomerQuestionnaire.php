<?php

namespace App\Models\API\GMO\V1;

use Illuminate\Database\Eloquent\Model;
use App\Models\API\GMO\V1\opportunity;
use App\Models\API\GMO\V1\partner;
use App\Models\API\GMO\V1\user;
use App\Models\API\GMO\V1\QuestionnaireAnswerContent;
use App\Models\API\GMO\V1\CustomerQuestionnaireAdditionalOpption;

class CustomerQuestionnaire extends Model
{
    protected $connection = 'gmoCrm';
    protected $table = 'app_customer_questionnaires';

    protected $fillable = [
        'id',
        'ua',
        'opportunity_id',
        'owner_id',
        'account_id',
		'is_aggregation_target',
        'created_at',
        'updated_at',
    ];
    protected $with = ['questionnaireAnswerContents', 'customerQuestionnaireAdditionalOpptions'];
    protected $appends = ['total_score'];

    public function opportunity()
    {
        return $this->hasOne(opportunity::class, 'id', 'opportunity_id');
    }

    public function partner()
    {
        return $this->hasOne(partner::class, 'id', 'account_id');
    }

    public function owner()
    {
        return $this->hasOne(user::class, 'id', 'owner_id');
    }

    public function questionnaireAnswerContents()
    {
        return $this->hasMany(QuestionnaireAnswerContent::class, 'customer_questionnaire_id', 'id');
    }

    public function customerQuestionnaireAdditionalOpptions()
    {
        return $this->hasMany(CustomerQuestionnaireAdditionalOpption::class, 'customer_questionnaire_id', 'id');
    }

    public function getTotalScoreAttribute()
    {
        return $this->attributes['total_score'] = $this->questionnaireAnswerContents->sum('acquisition_points');
    }
}
