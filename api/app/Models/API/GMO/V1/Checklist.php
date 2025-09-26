<?php

namespace App\Models\API\GMO\V1;

use App\Models\API\GMO\V1\ChecklistGroup;
use App\Models\API\GMO\V1\Houjin\companyChecklist;
use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    protected $table = 'app_checklists';
    protected $fillable = [
        'id',
        'checklist_group_id',
        'name',
        'note',
        'created_at',
        'updated_at',
    ];

    public function checklistGroupDetail()
    {
        return $this->hasOne(ChecklistGroup::class, 'id', 'checklist_group_id');
    }

    public function companys()
    {
        return $this->hasMany(companyChecklist::class, 'checklist_id');
    }
}
