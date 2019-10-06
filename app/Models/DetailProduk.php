<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

use DB;
use App\Models\Varian;

class DetailProduk extends Model
{
    protected $fillable = ['nama', 'deskripsi', 'berat', 'satuan_berat', 'kategori_id'];

    public function preorder()
    {
        return $this->hasOne(Preorder::class);
    }
    
    public function requesting()
    {
        return $this->hasOne(Requesting::class);
    }

    public function doTrip()
    {
        return $this->hasOne(DoTrip::class);
    }

    public function gambar()
    {
        return $this->hasMany(Gambar::class);
    }

    public function varian()
    {
        return $this->hasMany(Varian::class);
    }
    
    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public static function saveDetail(Request $request)
    {
        $detail = new DetailProduk;
        try
        {
            DB::beginTransaction();
            $detail->nama = $request->nama;
            // $detail->harga = $request->harga;
            $detail->deskripsi = $request->deskripsi;

            if(isset($request->berat))
                $detail->berat = $request->berat;
            
            if(isset($request->satuan_berat))
                $detail->satuan_berat = $request->satuan_berat;

            $detail->kategori_id = $request->kategori_id;
            $detail->save();

            if(isset($request->varian))
            {
                $varia = explode('~@~', $request->varian);
                $harga = explode('~@~', $request->harga);

                if(count($varia) == count($harga))
                {
                    foreach($varia as $key=>$variasi)
                    {
                        $varian = new Varian;
                        $varian->nama = $variasi;
                        $varian->harga = $harga[$key];
                        $varian->detail_produk_id = $detail->id;
                        $varian->save();
                    }
                }
                else
                {
                    DB::rollBack();
                    return response()->json(array("error"=>"Ada varian yang tidak memiliki harga"));
                }
            }
            else{
                $varian = new Varian;
                $varian->nama = "default";
                $varian->harga = $request->harga;
                $varian->detail_produk_id = $detail->id;
                $varian->save();
            }

            if(isset($request->gambar1))
            {
                $path = Gambar::savePictureToServer($request->gambar1);
                $gbr = new Gambar;
                $gbr->path_gambar = $path;
                $gbr->detail_produk_id = $detail->id;
                $gbr->save();
            }
            if(isset($request->gambar2))
            {
                $path = Gambar::savePictureToServer($request->gambar2);
                $gbr = new Gambar;
                $gbr->path_gambar = $path;
                $gbr->detail_produk_id = $detail->id;
                $gbr->save();
            }
            if(isset($request->gambar3))
            {
                $path = Gambar::savePictureToServer($request->gambar3);
                $gbr = new Gambar;
                $gbr->path_gambar = $path;
                $gbr->detail_produk_id = $detail->id;
                $gbr->save();
            }
            if(isset($request->gambar4))
            {
                $path = Gambar::savePictureToServer($request->gambar4);
                $gbr = new Gambar;
                $gbr->path_gambar = $path;
                $gbr->detail_produk_id = $detail->id;
                $gbr->save();
            }
            if(isset($request->gambar5))
            {
                $path = Gambar::savePictureToServer($request->gambar5);
                $gbr = new Gambar;
                $gbr->path_gambar = $path;
                $gbr->detail_produk_id = $detail->id;
                $gbr->save();
            }
        } catch(Exception $e)
        {
            DB::rollBack();
            return response()->json(array('error'=>$e));
        }
        DB::commit();
        return $detail->id;
    }

    public static function createVarian(Request $request)
    {
        $detail = new DetailProduk;
        try
        {
            DB::beginTransaction();
            $detail->nama = $request->nama;
            $detail->deskripsi = $request->deskripsi;
            
            if(isset($request->berat))
                $detail->berat = $request->berat;
            
            if(isset($request->satuan_berat))
                $detail->satuan_berat = $request->satuan_berat;

            $detail->kategori_id = $request->kategori_id;
            $detail->save();
            
            $varian = new Varian;
            $varian->nama = "default";
            $varian->harga = $request->harga;
            $varian->detail_produk_id = $detail->id;
            $varian->save();

            if(isset($request->gambar1))
            {
                $path = Gambar::savePictureToServer($request->gambar1);
                $gbr = new Gambar;
                $gbr->path_gambar = $path;
                $gbr->detail_produk_id = $detail->id;
                $gbr->save();
            }
            if(isset($request->gambar2))
            {
                $path = Gambar::savePictureToServer($request->gambar2);
                $gbr = new Gambar;
                $gbr->path_gambar = $path;
                $gbr->detail_produk_id = $detail->id;
                $gbr->save();
            }
            if(isset($request->gambar3))
            {
                $path = Gambar::savePictureToServer($request->gambar3);
                $gbr = new Gambar;
                $gbr->path_gambar = $path;
                $gbr->detail_produk_id = $detail->id;
                $gbr->save();
            }
            if(isset($request->gambar4))
            {
                $path = Gambar::savePictureToServer($request->gambar4);
                $gbr = new Gambar;
                $gbr->path_gambar = $path;
                $gbr->detail_produk_id = $detail->id;
                $gbr->save();
            }
            if(isset($request->gambar5))
            {
                $path = Gambar::savePictureToServer($request->gambar5);
                $gbr = new Gambar;
                $gbr->path_gambar = $path;
                $gbr->detail_produk_id = $detail->id;
                $gbr->save();
            }
        } catch(Exception $e)
        {
            DB::rollBack();
            return false;
        }
        DB::commit();
        return $detail->id;
    }
}
