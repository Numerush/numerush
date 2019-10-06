<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

use DB;

class DefaultKurir extends Model
{
    protected $fillable = ['user_id','kurir_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kurir()
    {
        return $this->belongsTo(Kurir::class);
    }

    public static function addDefault(Request $request)
    {
        try
        {
            DB::beginTransaction();
            $kurirs = explode('~@~', $request->kurir_id);

            $default = DefaultKurir::where('user_id', $request->user_id)->get();
            foreach($default as $def)
                $def->delete();

            foreach($kurirs as $kurir)
            {
                $data = DefaultKurir::firstOrCreate(['user_id'=>$request->user_id, 'kurir_id'=> $kurir]);
            }

        } catch(\Exception $e)
        {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return true;
    }

    public static function deleteDefault(Request $request)
    {
        try
        {
            DB::beginTransaction();
            $data = DefaultKurir::updateOrCreate(['user_id'=>$request->user_id],['alamat_id'=> $request->alamat_id]);

        } catch(\Exception $e)
        {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return true;
    }
}
