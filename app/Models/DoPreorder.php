<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use DB;

class DoPreorder extends Model
{
    //user_id orang yg offer
    protected $fillable = ['user_id', 'preoder_id', 'status', 'jumlah', 'dikirim_ke', 'varian_id', 'rincian'];

    //untuk tahu siapa yg melakukan preorder
    public function user()
    {
        return $this->belongsTo("App\User");
    }

    public function varian()
    {
        return $this->belongsTo(Varian::class);
    }

    public function dikirimke()
    {
        return $this->belongsTo(Alamat::class, 'dikirim_ke');
    }

    public function preorder()
    {
        return $this->belongsTo(Preorder::class);
    }

    public static function saveDoPreorder(Request $request)
    {
        $data = new DoPreorder;
        try
        {
            DB::beginTransaction();
            $data->user_id = $request->user_id;
            $data->preorder_id = $request->preorder_id;
            $data->dikirim_ke = $request->dikirim_ke;
            $data->jumlah = $request->jumlah;
            $data->varian_id = $request->varian_id;
            //otomatis lgs diterima karena daftar preorder pasti diterima
            $data->status = 'terima';
            $data->rincian = $request->rincian;
            // $data->expired = $request->expired;
            $data->save();

            if(!BoxTitipan::saveToBox($data->id, 0))
                throw new Exception('Error save box');
        } catch(Exception $e)
        {
            DB::rollBack();
            return false;
        }
        DB::commit();
        return $data->id;
    }
}
