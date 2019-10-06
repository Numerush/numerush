<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Share extends Model
{
    protected $fillable = ['postdata_id', 'user_id', 'postdata_type'];

    public function user()
    {
        return $this->belongsTo("App\User");
    }

    public function postdata()
    {
        return $this->morphTo();
    }
}
