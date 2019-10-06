<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;
use App\Models\Like;
use App\Transformers\LikeTransformer;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class LikeController extends Controller
{
    private $fractal;
    private $likeTransformer;

    function __construct(Manager $fractal, LikeTransformer $likeTransformer)
    {
        $this->fractal = $fractal;
        $this->likeTransformer = $likeTransformer;
    }

    public function myWishlist()
    {
        $currentUser = User::getCurrentUser();

        $likes = Like::where('user_id', $currentUser->id)->get(); // Get users from DB
        $likes = new Collection($likes, $this->likeTransformer); // Create a resource collection transformer
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        $this->fractal->parseIncludes('user,post,post.user,post.detail,post.dibelidari,post.detail.gambar,post.detail.kategori,post.detail.varian'); // parse includes
        $likes = $this->fractal->createData($likes); // Transform data

        return $likes->toArray(); // Get transformed array of data
    }

    public function saveLiked(Request $request, $tipe)
    {
        $currentUser = User::getCurrentUser();
        $request->user_id = $currentUser->id;

        if(Like::saveLikedPost($request, $tipe))
            return response()->json(['message'=>'Berhasil mengubah status wishlist post']);
        else
            return response()->json(['message'=>'Gagal mengubah status wishlist post']);
    }
}
