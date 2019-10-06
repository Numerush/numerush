<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

use DB;

class PostKurir extends Model
{
    protected $fillable = ['kurir_id','postdata_id', 'postdata_type'];

    public function kurir()
    {
        return $this->belongsTo(Kurir::class);
    }

    public function postdata()
    {
        return $this->morphTo();
    }

    public static function addPostKurir($kurir_id, $post_id, $tipe)
    {
        try{
            DB::beginTransaction();

            $kurirs = explode('~@~', $kurir_id);
            if($tipe == 0)
                $tipepost = 'App\Models\Preorder';
            else if($tipe == 1)
                $tipepost = 'App\Models\DoRequesting';
            else if($tipe == 2)
                $tipepost = 'App\Models\Trip';

            foreach($kurirs as $kurir)
            {
                $postkurir = new PostKurir;
                $postkurir->kurir_id = $kurir;
                $postkurir->postdata_id = $post_id;
                $postkurir->postdata_type = $tipepost;
                $postkurir->save();
            }

        } catch(Exception $e)
        {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return true;
    }
}
