<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

use Cache;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'verified', 'avatar', 'telepon', 'last_online', 'uid', 'rincian'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function getCurrentUser()
    {
        if(auth('api')->guest())
            return false;
        else
            return auth('api')->user();
    }

    public function isOnline()
    {
        return Cache::has('user-is-online-' . $this->id);
    }

    public function follower(){
        return $this->hasMany('App\Models\Follower', 'follower_user_id');
    }

    public function following(){
        return $this->hasMany('App\Models\Follower', 'user_id');
    }

    public function review(){
        return $this->hasMany('App\Models\Review', 'user_id');
    }

    public function reviewer(){
        return $this->hasMany('App\Models\Review', 'reviewer_user_id');
    }

    public function verifyUser() {
        return $this->hasOne('App\Models\VerifyUser');
    }

    public function notifikasi() {
        return $this->hasMany('App\Models\NotifikasiUser');
    }

    public function box_titipan() {
        return $this->hasMany('App\Models\BoxTitipan');
    }

    public function trip() {
        return $this->hasMany('App\Models\Trip');
    }

    public function preorder() {
        return $this->hasMany('App\Models\Preorder');
    }
    
    public function requesting() {
        return $this->hasMany('App\Models\Requesting');
    }

    public function doPreorder() {
        return $this->hasMany('App\Models\DoPreorder');
    }
    
    public function doRequesting() {
        return $this->hasMany('App\Models\DoRequesting');
    }
}
