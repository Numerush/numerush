<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

use DB;

class DefaultAlamat extends Model
{
    protected $fillable = ['user_id','alamat_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function alamat()
    {
        return $this->belongsTo(Alamat::class);
    }

    public static function changeDefault(Request $request)
    {
        try
        {
            DB::beginTransaction();
            $alamat = DefaultAlamat::updateOrCreate(['user_id'=>$request->user_id],['alamat_id'=> $request->alamat_id]);

        } catch(\Exception $e)
        {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return true;
    }
}
