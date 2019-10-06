<?php
namespace App\Transformers;

use App\Models\Like;
use League\Fractal\TransformerAbstract;

class LikeTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'user', 'post'
    ];

    public function transform(Like $like)
    {
        return [
            'id' => $like->id,
            'postdata_id' => $like->postdata_id,
            'user_id' => $like->user_id,
            'postdata_type' => $like->postdata_type,
        ];
    }

    public function includeUser(Like $like)
    {
        return $this->item($like->user, \App::make(UserTransformer::class), 'include');
    }

    public function includePost(Like $like)
    {
        if($like->tipe == 0)
            return $this->item($like->postdata, \App::make(PreorderTransformer::class),'include');
        else if($like->tipe == 1)
            return $this->item($like->postdata, \App::make(RequestingTransformer::class),'include');
        else if($like->tipe == 2)
            return $this->item($like->postdata, \App::make(TripTransformer::class),'include');
    }
}