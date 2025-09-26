<?php

namespace App\Models\API\GMO\V1\Houjin;

use App\Models\API\GMO\V1\Houjin\Houjin;
use App\Models\API\GMO\V1\Checklist;
use Illuminate\Database\Eloquent\Model;

class CompanyChecklist extends Model
{
    protected $table = 'app_company_checklists';
    protected $fillable = [
        'id',
        'company_id',
        'checklist_id',
        'created_at',
        'updated_at',
    ];

    public function companyDetail()
    {
        return $this->hasOne(Houjin::class, 'id', 'company_id');
    }

    public function checklist()
    {
        return $this->belongsTo(Checklist::class, 'checklist_id', 'id');
    }

    // public function checklistDetail()
    // {
    //     return $this->hasOne(Checklist::class, 'id', 'checklist_id');
    // }
}
