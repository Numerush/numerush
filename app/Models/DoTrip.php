<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

use App\Models\Detail_Produk;
use DB;

class DoTrip extends Model
{
    //user_id orang yg offer
    protected $fillable = ['user_id', 'trip_id', 'status', 'dikirim_ke', 'jumlah', 'varian_id', 'expired','detail_product_id'];

    public function varian()
    {
        return $this->belongsTo(Varian::class);
    }

    public function dikirimke()
    {
        return $this->belongsTo(Alamat::class, 'dikirim_ke');
    }

    //untuk tahu siapa yg request
    public function user()
    {
        return $this->belongsTo("App\User");
    }
    
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public static function saveDoTrip(Request $request)
    {
        $data = new DoTrip;
        try
        {
            DB::beginTransaction();
            $detailID = DetailProduk::createVarian($request);
            $varianID = DetailProduk::find($detailID)->varian->first()->id;
            $data->detail_product_id = $detailID;
            $data->user_id = $request->user_id;
            $data->trip_id = $request->trip_id;
            // $data->dibeli_dari = $request->dibeli_dari;
            $data->dikirim_ke = $request->dikirim_ke;
            $data->jumlah = $request->jumlah;
            $data->varian_id = $varianID;
            $data->expired = $request->expired;
            $data->save();
        } catch(Exception $e)
        {
            DB::rollBack();
            return false;
        }
        DB::commit();
        return $data->id;
    }
}
