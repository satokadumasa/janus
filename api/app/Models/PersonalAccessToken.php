<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PersonalAccessToken extends Model
{
    protected $table = 'personal_access_tokens';
    /**
     * @var array
     */
    protected $fillable = [
        'tokenable_type',
        'tokenable_id',
        'name',
        'token',
        'abilities',
        'last_used_at',
        'expires_at',
    ];
   
}