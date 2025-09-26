<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class EmailAddress extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'type_id',
        'meiladdress',
        'created_at',
        'updated_at',
    ];
   
}