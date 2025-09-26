<?php

namespace App\Models\API\GMO\V1;

use Illuminate\Database\Eloquent\Model;
use App\Models\API\GMO\V1\account;
use App\Models\API\GMO\V1\opportunity;
class Adjoin extends Model
{
    protected $table = "app_adjoins";

    protected $fillable = [
        'opportunity_id',
        'account_id',
        'work_date',
        'created_at',
        'updated_at',
    ];

    /**
     * partnre()
     *
     * @return void
     */
    public function partnre()
    {
        return $this->belongsTo(account::class, 'account_id', 'id');
    }

    /**
     * opportunity()
     *
     * @return void
     */
    public function opportunity()
    {
        return $this->belongsTo(opportunity::class, 'opportunity_id', 'id');
    }
}
