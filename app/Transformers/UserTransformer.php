<?php
namespace App\Transformers;

use App\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'review'
    ];

    public function transform(User $user)
    {
        $rating = $user->review()->pluck('rating')->avg();
        if(is_null($rating))
            $rating = 0;

        $follower = $user->follower()->count();
        $following = $user->following()->count();
        $box_titipan = $user->box_titipan()->count();
        $notifikasi = $user->notifikasi()->where('already_read',0)->count();

        $status = 'offline';
        if($user->isOnline())
            $status = 'online';

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => env('APP_URL') . '/' . $user->avatar,
            'telepon' => $user->telepon,
            'rating' => $rating,
            'follower' => $follower,
            'following' => $following,
            'box_titipan' => $box_titipan,
            'notifikasi' => $notifikasi,
            'status' => $status,
            'last_online' => $user->last_online,
            'uid' => $user->uid,
            'rincian' => $user->rincian,
        ];
    }

    public function includeReview(User $user)
    {
        return $this->collection($user->review->take(3), \App::make(ReviewTransformer::class), 'include');
    }
}