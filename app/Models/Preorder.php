<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

use App\Models\DetailProduk;
use App\Models\PostKurir;

use DB;

class Preorder extends Model
{
    protected $fillable = ['user_id', 'dibeli_dari', 'dikirim_dari', 'estimasi_pengiriman', 'expired', 'detail_produk_id'];

    public function detail_produk()
    {
        return $this->belongsTo(DetailProduk::class);
    }

    public function user()
    {
        return $this->belongsTo("App\User");
    }

    public function dibelidari()
    {
        return $this->belongsTo(Kota::class, 'dibeli_dari');
    }

    // public function dikirimDari()
    // {
    //     return $this->belongsTo(Kota::class, 'dikirim_dari');
    // }

    public function do_preorder()
    {
        return $this->hasMany(DoPreorder::class);
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



    public static function savePreorder(Request $request)
    {
        $preorder = new Preorder;
        try
        {
            DB::beginTransaction();
            $detilID = DetailProduk::saveDetail($request);

            if($detilID)
            {
                $preorder->user_id = $request->user_id;
                $preorder->dibeli_dari = $request->dibeli_dari;
                $preorder->dikirim_dari = $request->dikirim_dari;
                $preorder->estimasi_pengiriman = $request->estimasi_pengiriman;
                $preorder->expired = $request->expired;

                $preorder->detail_produk_id = $detilID;
                $preorder->save();

                $kurir = PostKurir::addPostKurir($request->kurir_id, $preorder->id, 0);

                if(!$kurir)
                {
                    throw new Exception('Gagal Menambahkan Kurir');
                }
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
        return $preorder->id;
    }
}
