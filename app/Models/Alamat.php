<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

use DB;
use App\Models\DefaultAlamat;
class Alamat extends Model
{
    protected $fillable = ['user_id','jalan', 'kode_pos' ,'kota_rajaongkir'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kota()
    {
        return $this->belongsTo(Kota::class);
    }

    public static function deleteAlamat(Request $request)
    {
        try
        {
            DB::beginTransaction();
            $def = DefaultAlamat::where('alamat_id',$request->alamat_id)->first();
            if(!empty($def))
            {
                if($def->user_id != $request->user_id)
                    return false;
                else
                    $def->delete();
            }
            $alamat = Alamat::find($request->alamat_id);
            if($alamat->user_id != $request->user_id)
                    return false;
            else
                $alamat->delete();
        } catch(\Exception $e)
        {
            // dd($e);
            DB::rollBack();
            return false;
        }

        DB::commit();
        return true;
    }

    public static function saveAlamat(Request $request)
    {
        try
        {
            DB::beginTransaction();
            $alamat = new Alamat;
            $alamat->user_id = $request->user_id;
            $alamat->jalan = $request->jalan;
            $alamat->kode_pos = $request->kode_pos;
            $alamat->kota_rajaongkir = $request->kota_rajaongkir;
            $alamat->save();

            if(Alamat::where('user_id',$request->user_id)->get()->count() == 1)
            {
                $def = new DefaultAlamat;
                $def->user_id = $request->user_id;
                $def->alamat_id = $alamat->id;
                $def->save();
            }
        } catch(\Exception $e)
        {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return true;
    }
}
