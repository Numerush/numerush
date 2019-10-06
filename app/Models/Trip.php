<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

use App\Models\PostKurir;

use DB;

class Trip extends Model
{
    protected $fillable = ['user_id', 'kota_asal', 'kota_tujuan', 'tanggal_berangkat', 'tanggal_kembali','rincian', 'estimasi_pengiriman','dikirim_dari'];

    public function user()
    {
        return $this->belongsTo("App\User");
    }

    public function asal()
    {
        return $this->belongsTo(Kota::class, 'kota_asal');
    }

    public function tujuan()
    {
        return $this->belongsTo(Kota::class, 'kota_tujuan');
    }

    // public function dikirimDari()
    // {
    //     return $this->belongsTo(Kota::class, 'dikirim_dari');
    // }

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

    public static function saveTrip(Request $request)
    {
        $trip = new Trip;
        try
        {
            DB::beginTransaction();
            $trip->user_id = $request->user_id;
            $trip->kota_asal = $request->kota_asal;
            $trip->kota_tujuan = $request->kota_tujuan;
            $trip->tanggal_berangkat = $request->tanggal_berangkat;
            $trip->tanggal_kembali = $request->tanggal_kembali;
            $trip->rincian = $request->rincian;
            $trip->estimasi_pengiriman = $request->estimasi_pengiriman;
            $trip->dikirim_dari = $request->dikirim_dari;
            $trip->expired = $request->expired;
            $trip->save();

            $kurir = PostKurir::addPostKurir($request->kurir_id, $trip->id, 2);
            if(!$kurir)
            {
                throw new Exception('Gagal Menambahkan Kurir');
            }
            
        } catch(Exception $e)
        {
            DB::rollBack();
            return false;
        }
        DB::commit();
        return $trip->id;
    }
}
