<?php

namespace App\Models;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

use DB;

class NotifikasiUser extends Model
{
    protected $fillable = ['user_id','pesan','already_read'];

    public function user()
    {
        return $this->belongsTo("App\User");
    }

    public function notifikasi()
    {
        return $this->belongsTo(Notifikasi::class);
    }

    public static function newNotifikasi($user_id, $pesan)
    {
        $notif = new NotifikasiUser;
        try
        {
            DB::beginTransaction();
            $notif->user_id = $user_id;
            $notif->pesan = $pesan;
            $notif->save();
        } catch(Exception $e)
        {
            DB::rollBack();
            return false;
        }
        DB::commit();
        return $notif->id; 
    }

}
