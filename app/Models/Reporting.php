<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

use DB;
use App\User;
use App\Models\Preorder;
use App\Models\Requesting;
use App\Models\Trip;
use App\Models\NotifikasiUser;

class Reporting extends Model
{
    protected $fillable = ['postdata_id', 'user_id', 'postdata_type', 'alasan'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function postdata()
    {
        return $this->morphTo();
    }

    public static function saveReport(Request $request, $tipe)
    {
        $data = new Reporting;
        try
        {
            DB::beginTransaction();
            if($tipe == 0)
            {
                $post= Preorder::find($request->post_id);
                $post_id= Preorder::find($request->post_id)->id;
                $user_reported= Preorder::find($request->post_id)->user_id;
                $post_type= 'App\Models\Preorder';
            }
            //request
            elseif($tipe == 1)
            {
                $post= Requesting::find($request->post_id);
                $post_id= Requesting::find($request->post_id)->id;
                $user_reported= Requesting::find($request->post_id)->user_id;
                $post_type= 'App\Models\Requesting';
            }
            //trip
            elseif($tipe == 2)
            {
                $post= Trip::find($request->post_id);
                $post_id= Trip::find($request->post_id)->id;
                $user_reported= Trip::find($request->post_id)->user_id;
                $post_type= 'App\Models\Trip';
            }

            $data = new Reporting;
            $data->user_id = $request->user_id;
            $data->alasan = $request->alasan;
            $data->postdata_id = $post_id;
            $data->postdata_type = $post_type;
            $data->save();

            NotifikasiUser::newNotifikasi($user_reported, $data->user->name . ' melakukan report pada post anda');
        } catch(Exception $e)
        {
            DB::rollBack();
            return false;
        }
        DB::commit();
        return $data->id;
    }
}
