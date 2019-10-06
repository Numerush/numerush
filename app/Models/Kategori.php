<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $fillable = ['nama_kategori'];

    // public function user()
    // {
    //     return $this->belongsTo("App\User");
    // }

    public function detail_produk()
    {
        return $this->hasMany(DetailProduk::class);
    }
}
