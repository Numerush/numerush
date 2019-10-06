<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;
use App\Models\DetailProduk;
use App\Models\Follower;

use App\Transformers\DetailProdukTransformer;

use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;

use Carbon\Carbon;

class TimelineController extends Controller
{
    private $fractal;
    private $detailProdukTransformer;

    function __construct(Manager $fractal, DetailProdukTransformer $detailProdukTransformer)
    {
        $this->fractal = $fractal;
        $this->detailProdukTransformer = $detailProdukTransformer;
    }

    public function followerTimeline()
    {
        $currentUser = User::getCurrentUser();

        $query = DetailProduk::paginate(30); // Get users from DB
        $follower = Follower::where('user_id', $currentUser->id)->get()->pluck('follower_user_id')->toArray();
        
        //hapus yang dari trip
        $query = $query->filter(function($value, $key) {
            return is_null($value->trip);
        });

        //filter user follower
        $query = $query->filter(function($value, $key) use($follower) {
            if(!is_null($value->preorder))
            {
                $shopper = $value->preorder->user_id;
                $expired = $value->preorder->expired;
            }
            else if(!is_null($value->requesting))
            {
                $shopper = $value->requesting->user_id;
                $expired = $value->requesting->expired;
            }

            if(isset($shopper))
            {
                return in_array($shopper, $follower) && $expired > Carbon::now()->timestamp;
            }
        });

        // filter kategori saja
        // $kategori = 28;
        // $query = $query->filter(function($value, $key) use($kategori) {
        //     return $value->kategori_id == $kategori;
        // });

        if(isset($_GET['filter']))
        {
            $filter = $_GET['filter'];

            if($filter == 'preorder')
            {
                //filter preorder saja
                $query = $query->filter(function($value, $key) {
                    return !is_null($value->preorder);
                });
            }
            else if($filter == 'requesting')
            {
                //filter request saja
                $query = $query->filter(function($value, $key) {
                    return !is_null($value->requesting);
                });
            }
        }

        $pageNum = \Illuminate\Pagination\Paginator::resolveCurrentPage('timeline');
        $detailProdukPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $query->forPage($pageNum,30), 
            $query->count(),
            30, 
            $pageNum, 
            ['path'=>url('api/my/timeline'),'pageName' => 'timeline',]);

        $detailProduk = new Collection($detailProdukPaginator->items(), $this->detailProdukTransformer); // Create a resource collection transformer
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        // $this->fractal->parseIncludes('varian,kategori,gambar,preorder,preorder.user,preorder.reviewer,trip,trip.user,trip.reviewer,requesting,requesting.user,requesting.reviewer');
        $this->fractal->parseIncludes('varian,kategori,gambar,' .
        'preorder,preorder.user,preorder.user.review,preorder.user.review.reviewer,preorder.dibelidari,preorder.dibelidari.negara,' .
        'requesting,requesting.user,requesting.user.review,requesting.user.review.reviewer,requesting.dibelidari,requesting.dibelidari.negara,' .
        'trip,trip.user,trip.user.review,trip.user.review.reviewer,trip.asal,trip.asal.negara,trip.tujuan,trip.tujuan.negara'); // parse includes
        
        $detailProduk->setPaginator(new IlluminatePaginatorAdapter($detailProdukPaginator));
        $detailProduk = $this->fractal->createData($detailProduk); // Transform data

        return $detailProduk->toArray(); // Get transformed array of data
    }
}
