<?php
namespace App\Transformers;

use App\Models\Follower;
use App\User;
use App\Models\Trip;
use App\Models\HasSeen;
use App\Models\Like;
use League\Fractal\TransformerAbstract;

class TripTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'user', 'asal', 'tujuan'
    ];

    public function transform(Trip $trip)
    {
        $user = User::getCurrentUser();
        if($user){
            $like = Like::where([['postdata_id',$trip->id],
            ['postdata_type','App\Models\Trip'],
            ['user_id',$user->id]])->first();

            $data = HasSeen::where([['postdata_id',$trip->id],
            ['postdata_type','App\Models\Trip'],
            ['user_id',$user->id]])->first();
            if($data)
            {
                //data ada maka sudah dilihat
                $seen = 1;
            }
            else
            {
                $seen = 0;
            }
            if($like)
            {
                //data ada di like/wishlist
                $is_wishlist = 1;
            }
            else
            {
                $is_wishlist = 0;
            }
            if(Follower::where([['user_id',$user->id],['follower_user_id',$trip->user->id]])->exists()){
                $is_followed=1;
            }
            else
            {
                $is_followed=0;
            }
        }
        else
        {
            $seen = 0;
            $is_wishlist = 0;
            $is_followed=0;
        }

        return [
            'id' => $trip->id,
            // 'kota_asal' => $trip->kota_asal, 
            // 'kota_tujuan' => $trip->kota_tujuan, 
            'tanggal_berangkat' => $trip->tanggal_berangkat, 
            'tanggal_kembali' => $trip->tanggal_kembali,
            'rincian' => $trip->rincian, 
            'estimasi_pengiriman' => $trip->estimasi_pengiriman,
            'dikirim_dari' => $trip->dikirim_dari,
            'seen' => $seen,
            'is_wishlist' => $is_wishlist,
            'followed'=>$is_followed,
        ];
    }

    public function includeUser(Trip $trip)
    {
        return $this->item($trip->user, \App::make(UserTransformer::class), 'include');
    }

    public function includeAsal(Trip $trip)
    {
        return $this->item($trip->asal, \App::make(KotaTransformer::class), 'include');
    }

    public function includeTujuan(Trip $trip)
    {
        return $this->item($trip->tujuan, \App::make(KotaTransformer::class), 'include');
    }
}