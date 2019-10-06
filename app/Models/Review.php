<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['pesan', 'rating', 'user_id', 'reviewer_user_id'];

    public function user()
    {
        return $this->belongsTo("App\User", 'user_id');
    }

    public function reviewer()
    {
        return $this->belongsTo("App\User", 'reviewer_user_id');
    }
}
