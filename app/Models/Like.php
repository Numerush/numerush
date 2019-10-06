<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

use DB;
use App\Models\Preorder;
use App\Models\Requesting;
use App\Models\Trip;
use App\Models\NotifikasiUser;

class Like extends Model
{
    protected $fillable = ['postdata_id', 'user_id', 'postdata_type'];

    public function user()
    {
        return $this->belongsTo("App\User");
    }

    public function postdata()
    {
        return $this->morphTo();
    }

    public static function saveLikedPost(Request $request, $tipe)
    {
        try
        {
            DB::beginTransaction();
            //preorder
            if($tipe == 0)
            {
                $post_id= Preorder::find($request->post_id)->id;
                $user_liked= Preorder::find($request->post_id)->user_id;
                $post_type= 'App\Models\Preorder';
            }
            //request
            elseif($tipe == 1)
            {
                $post_id= Requesting::find($request->post_id)->id;
                $user_liked= Requesting::find($request->post_id)->user_id;
                $post_type= 'App\Models\Requesting';
            }
            //trip
            elseif($tipe == 2)
            {
                $post_id= Trip::find($request->post_id)->id;
                $user_liked= Trip::find($request->post_id)->user_id;
                $post_type= 'App\Models\Trip';
            }

            $old = Like::where([['postdata_id','=',$post_id],
            ['postdata_type','=', $post_type],
            ['user_id','=', $request->user_id]])->first();

            $data = new Like;
            $data->user_id = $request->user_id;
            $data->postdata_id = $post_id;
            $data->postdata_type = $post_type;
            
            if($old)
            {
                $old->delete();
                NotifikasiUser::newNotifikasi($user_liked, $data->user->name . ' menghapus post anda ke wishlist');
            }
            else{
                $data->save();
                NotifikasiUser::newNotifikasi($user_liked, $data->user->name . ' memasukkan post anda ke wishlist');
            }

        } catch(\Exception $e)
        {
            DB::rollBack();
            dd($e);
        }
        
        DB::commit();
        return true;
    }
}
