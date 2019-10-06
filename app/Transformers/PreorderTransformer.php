<?php
namespace App\Transformers;

use App\User;
use App\Models\Preorder;
use App\Models\HasSeen;
use App\Models\Like;
use League\Fractal\TransformerAbstract;

class PreorderTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'detail', 'user', 'dibelidari'
    ];

    public function transform(Preorder $preorder)
    {
        $user = User::getCurrentUser();
        if($user){
            $like = Like::where([['postdata_id',$preorder->id],
            ['postdata_type','App\Models\Preorder'],
            ['user_id',$user->id]])->first();

            $data = HasSeen::where([['postdata_id',$preorder->id],
            ['postdata_type','App\Models\Preorder'],
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
                $is_wishlist = 1;
            }
            else
            {
                $is_wishlist = 0;
            }
        }
        else
        {
            $seen = 0;
            $is_wishlist = 0;
        }
        return [
            'id' => $preorder->id,
            'user_id' => $preorder->user_id, 
            // 'dibeli_dari' => $preorder->dibeli_dari, 
            'dikirim_dari' => $preorder->dikirim_dari, 
            'estimasi_pengiriman' => $preorder->estimasi_pengiriman, 
            'expired' => $preorder->expired, 
            'seen' => $seen, 
            'is_wishlist' => $is_wishlist, 
            // 'detail_produk_id' => $preorder->detail_produk_id
        ];

    }

    public function includeUser(Preorder $preorder)
    {
        return $this->item($preorder->user, \App::make(UserTransformer::class), 'include');
    }

    public function includeDetail(Preorder $preorder)
    {
        return $this->item($preorder->detail_produk, \App::make(DetailProdukTransformer::class), 'include');
    }

    public function includeDibeliDari(Preorder $preorder)
    {
        return $this->item($preorder->dibelidari, \App::make(KotaTransformer::class), 'include');
    }
}