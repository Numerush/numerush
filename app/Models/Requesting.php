<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

use App\Models\DetailProduk;

use DB;

class Requesting extends Model
{
    protected $fillable = ['user_id', 'jumlah', 'dibeli_dari', 'dikirim_ke', 'expired', 'detail_produk_id'];

    public function detail_produk()
    {
        return $this->belongsTo(DetailProduk::class);
    }

    public function user()
    {
        return $this->belongsTo("App\User");
    }

    public function dikirimke()
    {
        return $this->belongsTo(Alamat::class, 'dikirim_ke');
    }

    public function dibelidari()
    {
        return $this->belongsTo(Kota::class, 'dibeli_dari');
    }

    public function do_requesting()
    {
        return $this->hasMany(DoRequesting::class);
    }

    public function detail_titipan()
    {
        return $this->morphMany(DetailTitipan::class, 'postdata');
    }
    public function box_titipan()
    {
        return $this->morphMany(BoxTitipan::class, 'postdata');
    }
    public function like()
    {
        return $this->morphMany(Like::class, 'postdata');
    }
    public function share()
    {
        return $this->morphMany(Share::class, 'postdata');
    }
    public function comment()
    {
        return $this->morphMany(Comment::class, 'postdata');
    }
    public function reporting()
    {
        return $this->morphMany(Reporting::class, 'postdata');
    }

    public static function saveRequesting(Request $request)
    {
        $requesting = new Requesting;
        try
        {
            DB::beginTransaction();
            $detilID = DetailProduk::createVarian($request);

            if($detilID)
            {
                $requesting->user_id = $request->user_id;
                $requesting->jumlah = $request->jumlah;
                $requesting->dibeli_dari = $request->dibeli_dari;
                $requesting->dikirim_ke = $request->dikirim_ke;
                $requesting->expired = $request->expired;

                $requesting->detail_produk_id = $detilID;
                $requesting->save();
            }
            else
            {
                throw new Exception('Gagal Menambahkan Detil');
            }
        } catch(Exception $e)
        {
            DB::rollBack();
            return false;
        }
        DB::commit();
        return $requesting->id;
    }
}
