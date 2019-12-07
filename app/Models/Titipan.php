<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

use DB;

class Titipan extends Model
{
    protected $fillable = ['user_id', 'shopper_id', 'total_harga', 'total_harga_kirim', 'kurir_id', 'metode_bayar', 'bukti_bayar', 'status_bayar', 'estimasi_pengiriman', 'nomer_resi', 'dibeli_dari', 'dikirim_dari','dikirim_ke', 'status_transaksi_id', 'kode_unik'];

    public function status_transaksi()
    {
        return $this->belongsTo(StatusTransaksi::class);
    }

    public function user()
    {
        return $this->belongsTo("App\User");
    }

    public function dikirimke()
    {
        return $this->belongsTo(Alamat::class, 'dikirim_ke');
    }

    public static function saveToTitipan(Request $request)
    {
        try
        {
            DB::beginTransaction();

            $box_ids = explode('~@~', $request->box_ids);
            $isi_box = BoxTitipan::whereIn('id', $box_ids)->get();
            
    
            $jumlahShopper = count($isi_box->pluck('shopper_id')->groupBy('shopper_id'));
            // dd($jumlahShopper);
            
            if($jumlahShopper > 1)
            {
                $bagi_isi = $isi_box->groupBy('shopper_id');
    
                foreach($bagi_isi as $isiTemp)
                {
                    foreach($isiTemp as $isi)
                {
                    $titipan = new Titipan;
                    $titipan->user_id = $isi->user_id;
                    $titipan->shopper_id = $isi->shopper_id;
                    $titipan->total_harga = 0;
                    $titipan->total_harga_kirim = $request->total_harga_kirim;
                    $titipan->metode_bayar = $request->metode_bayar;
                    $titipan->kurir_id = $request->kurir_id;
                    $titipan->estimasi_pengiriman = $isi->estimasi_pengiriman;
                    $titipan->dibeli_dari = $isi->dibeli_dari;
                    $titipan->dikirim_dari = $isi->dikirim_dari;
                    $titipan->dikirim_ke = $isi->dikirim_ke;
                    $titipan->status_transaksi_id = 1;
                    $titipan->kode_unik = rand(1,999);
                    $titipan->save();
                    $total = 0;
                        $detil = new DetailTitipan;
                        $detil->harga = $isi->harga;
                        $detil->titipan_id = $titipan->id;
                        $detil->varian_id = $isi->varian_id;
                        $detil->postdata_type = $isi->postdata_type;
                        $detil->postdata_id = $isi->postdata_id;
                        $detil->save();
                        
                        $total += $isi->harga;
                    
    
                    $titipan->total_harga = $total;
                    $titipan->save();
                }
                }
            }
            else if($jumlahShopper == 1)
            {
                
                $titipan = new Titipan;
                
                $titipan->user_id = $isi_box->first()->user_id;
                $titipan->shopper_id = $isi_box->first()->shopper_id;
                $titipan->total_harga = 0;
                $titipan->total_harga_kirim = $request->total_harga_kirim;
                $titipan->metode_bayar = $request->metode_bayar;
                $titipan->kurir_id = $request->kurir_id;
                $titipan->estimasi_pengiriman = $isi_box->first()->estimasi_pengiriman;
                $titipan->dibeli_dari = $isi_box->first()->dibeli_dari;
                $titipan->dikirim_dari = $isi_box->first()->dikirim_dari;
                $titipan->dikirim_ke = $isi_box->first()->dikirim_ke;
                $titipan->status_transaksi_id = 1;
                $titipan->kode_unik = rand(1,999);
                
                $titipan->save();

                

                
    
                $total = 0;
    
                foreach($isi_box as $box)
                {
                    
                    $detil = new DetailTitipan;
                    $detil->harga = $box->harga;
                    $detil->titipan_id = $titipan->id;
                    $detil->varian_id = $box->varian_id;
                    $detil->postdata_type = $box->postdata_type;
                    $detil->postdata_id = $box->postdata_id;
                    $detil->save();
                    
                    $total += $box->harga;
                }
    
                $titipan->total_harga = $total;
                $titipan->save();
            }

            foreach($isi_box as $box)
            {
                $box->delete();
            }
        } catch(\Exception $e)
        {
            
            DB::rollBack();
            dd($e);
            return null;
        }

        DB::commit();
        return $titipan;
    }
}
