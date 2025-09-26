<?php

namespace App\Models\API\GMO\V1\Keiri;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $guard = 'kurapitaluser';
    protected $connection = 'gmoCrm';
    protected $table = 'app_users';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'loginext'
    ];
    protected $hidden = [
        'NewPassword', 'remember_token',
    ];
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
}
