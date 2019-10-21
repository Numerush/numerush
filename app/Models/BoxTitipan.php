<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

use DB;
use App\Models\Preorder;
use App\Models\Requesting;
use App\Models\Trip;

use App\Models\DoPreorder;
use App\Models\DoRequesting;
use App\Models\DoTrip;

use App\Models\DetailProduk;

class BoxTitipan extends Model
{
    //user_id orang yg offer
    protected $fillable = ['user_id', 'shopper_id', 'harga', 'estimasi_pengiriman','dibeli_dari', 'dikirim_dari','dikirim_ke', 'berat', 'varian_id', 'tipe', 'postdata_id', 'postdata_type'];


    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function shopper()
    {
        return $this->belongsTo('App\User', 'shopper_id');
    }

    public function varian()
    {
        return $this->belongsTo(Varian::class);
    }

    public function dikirimke()
    {
        return $this->belongsTo(Alamat::class, 'dikirim_ke');
    }

    public function postdata()
    {
        return $this->morphTo();
    }

    public static function saveToBox($offer_post_id, $tipe)
    {
        $box = new BoxTitipan;
        try
        {
            DB::beginTransaction();

            if($tipe == 0) //terima offer dari preorder
            {
                $offer = DoPreorder::find($offer_post_id);
                $detail = $offer->varian->detail_produk;
                $varian = $offer->varian;
                $user_id=$offer->user_id;
                $harga = $varian->harga * $offer->jumlah;
                $idPenjual = $offer->preorder->user->id;
                $estimasi = $offer->preorder->estimasi_pengiriman;
                $beli_dari = $offer->preorder->dibeli_dari;
                $post_id = $offer->preorder_id;

                $postdata_id = Preorder::find($post_id)->id;
                $postdata_type = 'App\Models\Preorder';

                $dari = $offer->preorder->dikirim_dari;
                $tujuan = $offer->dikirimke->id;
            }
            elseif($tipe == 1) //terima offer dari requesting
            {
                $offer = DoRequesting::find($offer_post_id);
                $estimasi = $offer->estimasi_pengiriman;
                $beli_dari = $offer->dibeli_dari;
                $detail = $offer->varian->detail_produk;
                $varian = $offer->varian;
                $user_id=$offer->requesting->user->id;
                $harga = $varian->harga;
                $idPenjual = $offer->user_id;
                $post_id = $offer->requesting_id;

                $postdata_id = Requesting::find($post_id)->id;
                $postdata_type = 'App\Models\Requesting';

                $dari = $offer->dikirim_dari;
                $tujuan = $offer->requesting->dikirimke->id;
            }
            elseif($tipe == 2) //terima offer dari trip
            {
                $offer = DoTrip::find($offer_post_id);
                $detail = $offer->varian->detail_produk;
                $varian = $offer->varian;
                $user_id=$offer->user_id;
                $harga = $varian->harga * $offer->jumlah;
                $idPenjual = $offer->trip->user->id;
                $estimasi = $offer->trip->estimasi_pengiriman;
                $beli_dari = $offer->trip->kota_tujuan;
                $post_id = $offer->trip_id;
                
                $postdata_id = Trip::find($post_id)->id;
                $postdata_type = 'App\Models\Trip';

                $dari = $offer->trip->dikirim_dari;
                $tujuan = $offer->dikirimke->id;
            }

            //berat dikali 1000 kalau kg
            if($detail->satuan_berat == "kg")
                $berat = $detail->berat * 1000;
            else
                $berat = $detail->berat;            

            if($tipe == 1)
            {
                if($offer->satuan_berat == "kg")
                $berat = $offer->berat * 1000;
                else
                $berat = $offer->berat;
            }

            $box->user_id = $user_id;
            $box->shopper_id = $idPenjual;
            $box->harga = $harga;
            $box->dibeli_dari = $beli_dari;
            $box->dikirim_dari = $dari;
            $box->dikirim_ke = $tujuan;
            $box->estimasi_pengiriman = $estimasi;
            $box->berat = $berat;
            $box->varian_id = $varian->id;
            $box->postdata_id = $postdata_id;
            $box->postdata_type = $postdata_type;
            $box->save();
        } catch(Exception $e)
        {
            DB::rollBack();
            return false;
        }
        DB::commit();
        return $box->id;
    }
}
