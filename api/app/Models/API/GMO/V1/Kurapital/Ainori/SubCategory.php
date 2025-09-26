<?php

namespace App\Models\API\GMO\V1\Kurapital\Ainori;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    //
 protected $connection = 'gmoCrm';
    protected $table = 'app_categories';
    public $timestamps = false;

    public function category(){
        return $this->belongsTo(SubCategory::class);
    }

}
