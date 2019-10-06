<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['post_id', 'user_id', 'tipe', 'komentar', 'postdata_id','postdata_type'];

    public function user()
    {
        return $this->belongsTo("App\User");
    }

    public function postdata()
    {
        return $this->morphTo();
    }
}
