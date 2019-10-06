<?php
namespace App\Transformers;

use App\Models\Follower;
use League\Fractal\TransformerAbstract;

class FollowerTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'user', 'follower'
    ];

    public function transform(Follower $follower)
    {
        return [
            'id' => $follower->id,
            'follower_user_id' => $follower->follower_user_id,
        ];
    }

    public function includeUser(Follower $follower)
    {
        return $this->item($follower->user, \App::make(UserTransformer::class),'include');
    }

    public function includeFollower(Follower $follower)
    {
        return $this->item($follower->follower, \App::make(UserTransformer::class),'include');
    }
}