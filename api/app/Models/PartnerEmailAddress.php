<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PartnerEmailAddress extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'partner_id',
        'email_address_id',
        'created_at',
        'updated_at',
    ];
   
}