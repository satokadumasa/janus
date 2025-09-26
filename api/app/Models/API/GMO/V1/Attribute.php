<?php

namespace App\Models\API\GMO\V1;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    //
    public function group()
    {
        return $this->belongsTo(AttributeGroup::class);
    }
    /*
    public function getNameAttribute($value)
    {
        $rootKey = "attribute.{$this->group->name}";
        return trans("{$rootKey}.{$value}");
    }
    */
}
