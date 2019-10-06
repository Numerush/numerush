<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Faker\Generator as Faker;

class Kota extends Model
{
    protected $fillable = ['nama_kota', 'negara_id'];

    public function negara()
    {
        return $this->belongsTo(Negara::class);
    }

    public function preorder()
    {
        return $this->hasMany(Preorder::class, 'dibeli_dari');
    }
    
    public function requesting()
    {
        return $this->hasMany(Requesting::class, 'dibeli_dari');
    }

    public function tripAsal()
    {
        return $this->hasMany(Trip::class, 'kota_asal');
    }

    public function tripTujuan()
    {
        return $this->hasMany(Trip::class, 'kota_tujuan');
    }
}
