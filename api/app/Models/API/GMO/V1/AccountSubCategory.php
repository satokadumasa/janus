<?php

namespace App\Models\API\GMO\V1;

use Illuminate\Database\Eloquent\Model;

class AccountSubCategory extends Model
{
    //
    protected $connection ='gmoCrm';
    protected $table ='app_account_subcategories';
    public $timestamps = false;
    protected $fillable =[
        // 'id',
        'account_id',
        'sub_category_id',
       // 'score',
    ];
    public function accountSubCategory()
    {
        return $this->belongsTo(accountSubCategory::class);
    }
    public function subCategoryDetail()
    {
        return $this->hasOne(SubCategory::class, 'id', 'sub_category_id');
    }

}
