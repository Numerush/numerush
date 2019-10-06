<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Negara extends Model
{
    protected $fillable = ['nama_negara', 'bendera_path', 'wallpaper'];
    
    public function kota()
    {
        return $this->hasMany(Kota::class);
    }
}
