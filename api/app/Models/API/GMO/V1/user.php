<?php

namespace App\Models\API\GMO\V1;
// use Tymon\JWTAuth\Contracts\JWTSubject;
// use Illuminate\Notifications\Notifiable;
// use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Support\Facades\Hash;



// use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Notifications\Notifiable;
// use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Models\API\V1\Task;

class user extends Model 
{
    // use HasApiTokens, Notifiable ;
    // protected $guard = 'kurapitaluser';
    protected $connection = 'gmoCrm';
    protected $table = 'app_users';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'email',
        'password',
        'familyname',
        'firstname',
        'username',
        'loginext'
    ];
    protected $hidden = [
        'NewPassword', 'remember_token',
    ];
    protected $appends = ['full_name'];
    // public function findForPassport($username)
    // {
    //     return $this->where('username', $username)->first();
    // }
    public function setPasswordAttribute($password) {
        $this->attributes['password'] = bcrypt($password);
    }
    public function findForPassport($username)
    {
        return $this->where('username', $username)->first();
    }
    public function validateForPassportPasswordGrant($password)
    {
        return Hash::check($password, $this->password);
    }

    public function tasks(){
        return $this->hasMany(Task::class, 'user_id', 'id');
    }

    public function createTasks(){
        return $this->hasMany(Task::class, 'create_user_id', 'id');
    }
    public function getFullNameAttribute()
    {
        return $this->attributes['full_name'] = $this->familyname . ' ' . $this->firstname;
    }
}