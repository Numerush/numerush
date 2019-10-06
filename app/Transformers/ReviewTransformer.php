<?php
namespace App\Transformers;

use App\Models\Review;
use League\Fractal\TransformerAbstract;

class ReviewTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'user', 'reviewer'
    ];

    public function transform(Review $review)
    {
        return [
            'id' => $review->id,
            'pesan' => $review->pesan,
            'rating' => $review->rating
        ];
    }

    public function includeUser(Review $review)
    {
        return $this->item($review->user, \App::make(UserTransformer::class),'include');
    }

    public function includeReviewer(Review $review)
    {
        return $this->item($review->reviewer, \App::make(UserTransformer::class),'include');
    }
}