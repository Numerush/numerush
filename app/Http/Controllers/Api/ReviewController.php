<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;
use App\Models\Review;
use App\Transformers\ReviewTransformer;

use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;

class ReviewController extends Controller
{
    private $fractal;
    private $reviewTransformer;

    function __construct(Manager $fractal, ReviewTransformer $reviewTransformer)
    {
        $this->fractal = $fractal;
        $this->reviewTransformer = $reviewTransformer;
    }

    public function myReview()
    {
        $reviewPaginator = Review::where('user_id',Auth::user()->id)->orderBy('created_at','desc')->paginate(10); // Get users from DB
        $review = new Collection($reviewPaginator->items(), $this->reviewTransformer); // Create a resource collection transformer
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        $this->fractal->parseIncludes('reviewer'); // parse includes
        $review->setPaginator(new IlluminatePaginatorAdapter($reviewPaginator));
        $review = $this->fractal->createData($review); // Transform data

        return $review->toArray(); // Get transformed array of data
    }

    public function getUserReview($user_id)
    {
        $reviewPaginator = Review::where('user_id',$user_id)->orderBy('created_at','desc')->paginate(10); // Get users from DB
        $review = new Collection($reviewPaginator->items(), $this->reviewTransformer); // Create a resource collection transformer
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        $this->fractal->parseIncludes('reviewer'); // parse includes
        $review->setPaginator(new IlluminatePaginatorAdapter($reviewPaginator));
        $review = $this->fractal->createData($review); // Transform data

        return $review->toArray(); // Get transformed array of data
    }
}
