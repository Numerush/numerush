<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    protected $fillable = ['user_id', 'follower_user_id'];

    public function user()
    {
        return $this->belongsTo("App\User");
    }

    public function follower()
    {
        return $this->belongsTo("App\User", 'follower_user_id');
    }
}
