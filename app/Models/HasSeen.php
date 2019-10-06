<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

use DB;

class HasSeen extends Model
{
    protected $fillable = ['user_id','postdata_id', 'postdata_type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function postdata()
    {
        return $this->morphTo();
    }

    public static function addSeen(Request $request)
    {
        try
        {
            if($request->tipe == 0)
                $postdata_type = 'App\Models\Preorder';
            else if($request->tipe == 1)
                $postdata_type = 'App\Models\Requesting';
            else if($request->tipe == 2)
                $postdata_type = 'App\Models\Trip';

            DB::beginTransaction();
            $data = HasSeen::firstOrCreate([
                'user_id' => $request->user_id,
                'postdata_id' => $request->post_id,
                'postdata_type' => $postdata_type
            ]);

        } catch(\Exception $e)
        {
            DB::rollBack();
            return false;
        }

        DB::commit();
        return true;
    }
}
