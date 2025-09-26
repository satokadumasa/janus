<?php

namespace App\Models\API\GMO\V1;

use Illuminate\Database\Eloquent\Model;
use App\Models\API\GMO\V1\Question;
use App\Models\API\GMO\V1\CustomerQuestionnaire;

class QuestionnaireAnswerContent extends Model
{
    protected $connection = 'gmoCrm';
    protected $table = 'app_questionnaire_answer_contents';

    protected $fillable = [
        'id',
        'customer_questionnaire_id',
        'question_id',
        'customer_satisfaction',
        'etc',
        'acquisition_points',
        'created_at',
        'updated_at',
    ];

    public function question()
    {
        return $this->hasOne(Question::class, 'id', 'question_id');
    }

    public function customerQuestionnaire()
    {
        return $this->belongsTo(CustomerQuestionnaire::class, 'id', 'customer_questionnaire_id');
    }

}
