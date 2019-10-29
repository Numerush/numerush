<?php
namespace App\Transformers;

use App\Models\Follower;
use App\User;
use App\Models\Requesting;
use App\Models\HasSeen;
use App\Models\Like;
use League\Fractal\TransformerAbstract;

class RequestingTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'detail', 'user', 'dibelidari', 'dikirimke'
    ];

    public function transform(Requesting $requesting)
    {
        $user = User::getCurrentUser();
        if($user){
            $like = Like::where([['postdata_id',$requesting->id],
            ['postdata_type','App\Models\Requesting'],
            ['user_id',$user->id]])->first();

            $data = HasSeen::where([['postdata_id',$requesting->id],
            ['postdata_type','App\Models\Requesting'],
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
                //data di like maka muncul
                $is_wishlist = 1;
            }
            else
            {
                $is_wishlist = 0;
            }
            if(Follower::where([['follower_user_id',$user->id],['user_id',$requesting->user->id]])->exists()){
                $is_followed=1;
            }
            else
            {
                $is_followed=0;
            }
        }
        else
        {
            //karena belum login maka belum dilihat dan tidak di like
            $seen = 0;
            $is_wishlist = 0;
            $is_followed=0;
        }

        return [
            'id' => $requesting->id,
            'user_id' => $requesting->user_id, 
            'jumlah' => $requesting->jumlah, 
            // 'dibeli_dari' => $requesting->dibeli_dari, 
            // 'dikirim_ke' => $requesting->dikirim_ke, 
            'expired' => $requesting->expired,
            'seen' => $seen,
            'is_wishlist' => $is_wishlist,
            'followed'=>$is_followed,
        ];
    }

    public function includeUser(Requesting $requesting)
    {
        return $this->item($requesting->user, \App::make(UserTransformer::class), 'include');
    }

    public function includeDetail(Requesting $requesting)
    {
        return $this->item($requesting->detail_produk, \App::make(DetailProdukTransformer::class), 'include');
    }

    public function includeDibeliDari(Requesting $requesting)
    {
        return $this->item($requesting->dibelidari, \App::make(KotaTransformer::class), 'include');
    }

    public function includeDikirimKe(Requesting $requesting)
    {
        return $this->item($requesting->dikirimke, \App::make(AlamatTransformer::class), 'include');
    }
}