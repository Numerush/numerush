<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use DB;

use App\Models\Requesting;
use App\Models\PostKurir;

class DoRequesting extends Model
{
    //user_id orang yg offer
    protected $fillable = ['user_id', 'requesting_id', 'status', 'dibeli_dari', 'dikirim_dari','estimasi_pengiriman','berat','satuan_berat','varian_id', 'rincian', 'expired'];

    //untuk tahu siapa yg request
    public function user()
    {
        return $this->belongsTo("App\User");
    }

    public function varian()
    {
        return $this->belongsTo(Varian::class);
    }

    public function requesting()
    {
        return $this->belongsTo(Requesting::class);
    }

    public function dibelidari()
    {
        return $this->belongsTo(Kota::class, 'dibeli_dari');
    }

    public static function rejectAllExcept($do_requesting_id, $requesting_id)
    {
        $otherReq = DoRequesting::whereIn('requesting_id', [$requesting_id])->whereNotIn('id', [$do_requesting_id])->get();
        try
        {
            DB::beginTransaction();

            foreach($otherReq as $req)
            {
                $req->status = 'tolak';
                $req->save();
            }

            DB::commit();
        } catch(\Exception $e)
        {
            DB::rollBack();
            return false;
        }
        return true;
    }

    public static function saveDoRequesting(Request $request)
    {
        $data = new DoRequesting;
        try
        {
            DB::beginTransaction();
            
            $data->user_id = $request->user_id;
            $data->requesting_id = $request->requesting_id;
            $data->berat = $request->berat;
            $data->satuan_berat = $request->satuan_berat;
            $data->dibeli_dari = $request->dibeli_dari;
            $data->dikirim_dari = $request->dikirim_dari;
            $data->estimasi_pengiriman = $request->estimasi_pengiriman;

            $requesting = Requesting::find($request->requesting_id);
            $data->varian_id = $requesting->detail_produk->varian->first()->id;
            $data->rincian = $request->rincian;
            $data->expired = $request->expired;
            $data->save();

            $kurir = PostKurir::addPostKurir($request->kurir_id, $data->id, 1);

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
        return $data->id;
    }
}