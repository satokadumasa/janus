<?php

namespace App\Models\API\GMO\V1;

use App\Models\API\GMO\V1\Checklist;
use Illuminate\Database\Eloquent\Model;

class ChecklistGroup extends Model
{
    protected $table = 'app_checklist_groups';
    protected $fillable = [
        'id',
        'name',
        'created_at',
        'updated_at',
    ];

    public function checklists()
    {
        return $this->hasMany(Checklist::class, 'checklist_group_id');
    }
}
