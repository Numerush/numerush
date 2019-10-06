<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class Varian extends Model
{
    protected $fillable = ['nama', 'harga', 'detail_produk_id'];

    public function detail_produk()
    {
        return $this->belongsTo(DetailProduk::class);
    }
}
