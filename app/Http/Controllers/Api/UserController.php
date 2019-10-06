<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;
use App\Models\Gambar;
use App\Models\DetailProduk;
use App\Models\Review;
use App\Models\Follower;
use App\Models\DoPreorder;
use App\Models\DoRequesting;
use App\Models\DoTrip;
use App\Models\Kategori;
use App\Models\Preorder;
use App\Models\Trip;
use App\Models\Requesting;
use App\Models\Reporting;
use App\Models\HasSeen;
use App\Models\NotifikasiUser;

use App\Transformers\UserTransformer;
use App\Transformers\DetailProdukTransformer;
use App\Transformers\ReviewTransformer;
use App\Transformers\DoRequestingTransformer;
use App\Transformers\DoPreorderTransformer;
use App\Transformers\DoTripTransformer;
use App\Transformers\KategoriTransformer;
use App\Transformers\FollowerTransformer;

use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;

use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    private $fractal;
    private $userTransformer;
    private $detailProdukTransformer;
    private $reviewTransformer;
    private $doRequestingTransformer;
    private $doPreorderTransformer;
    private $doTripTransformer;
    private $kategoriTransformer;
    private $followerTransformer;

    function __construct(Manager $fractal, UserTransformer $userTransformer, DetailProdukTransformer $detailProdukTransformer
        , ReviewTransformer $reviewTransformer, DoRequestingTransformer $doRequestingTransformer
        , DoPreorderTransformer $doPreorderTransformer, DoTripTransformer $doTripTransformer
        , KategoriTransformer $kategoriTransformer, FollowerTransformer $followerTransformer)
    {
        $this->fractal = $fractal;
        $this->userTransformer = $userTransformer;
        $this->detailProdukTransformer = $detailProdukTransformer;
        $this->reviewTransformer = $reviewTransformer;
        $this->doRequestingTransformer = $doRequestingTransformer;
        $this->doPreorderTransformer = $doPreorderTransformer;
        $this->doTripTransformer = $doTripTransformer;
        $this->kategoriTransformer = $kategoriTransformer;
        $this->followerTransformer = $followerTransformer;
    }

    public function user(Request $request) {
        $usersPaginator = User::where('id', $request->user()->id)->get();

        $users = new Collection($usersPaginator, $this->userTransformer);
        $users = $this->fractal->createData($users); // Transform data

        return $users->toArray();
    }

    public function index(Request $request)
    {
        $currentUser = User::getCurrentUser();
        $usersPaginator = User::where('id', '!=', $currentUser->id)->paginate(10);

        $users = new Collection($usersPaginator->items(), $this->userTransformer);
        $users->setPaginator(new IlluminatePaginatorAdapter($usersPaginator));

        $users = $this->fractal->createData($users); // Transform data

        return $users->toArray(); // Get transformed array of data
    }

    public function getKategori()
    {
        $kategoris = Kategori::all(); // Get users from DB
        $kategoris = new Collection($kategoris, $this->kategoriTransformer); // Create a resource collection transformer
        $kategoris = $this->fractal->createData($kategoris); // Transform data

        return $kategoris->toArray(); // Get transformed array of data
    }

    public function myRequestList()
    {
        $currentUser = User::getCurrentUser();
        $dataPaginator = DoRequesting::join('requestings','do_requestings.requesting_id','=','requestings.id')
        ->where('requestings.user_id', '=', $currentUser->id)
        ->select('do_requestings.*')
        ->get();

        $data = new Collection($dataPaginator, $this->doRequestingTransformer);
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        $this->fractal->parseIncludes('user,user.review,user.review.reviewer,varian,requesting,requesting.detail'); // parse includes
        $data = $this->fractal->createData($data); // Transform data

        return $data->toArray(); // Get transformed array of data
    }

    public function myPreorderList()
    {
        $currentUser = User::getCurrentUser();
        $dataPaginator = DoPreorder::join('preorders','do_preorders.preorder_id','=','preorders.id')
        ->where('preorders.user_id', '=', $currentUser->id)
        ->select('do_preorders.*')
        ->get();

        $data = new Collection($dataPaginator, $this->doPreorderTransformer);
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        $this->fractal->parseIncludes('user,user.review,user.review.reviewer,varian,preorder,preorder.detail,dikirimke,dikirimke.kota,dikirimke.kota.negara'); // parse includes
        $data = $this->fractal->createData($data); // Transform data

        return $data->toArray(); // Get transformed array of data
    }

    public function myTripList()
    {
        $currentUser = User::getCurrentUser();
        $dataPaginator = DoTrip::join('trips','do_trips.trip_id','=','trips.id')
        ->where('trips.user_id', '=', $currentUser->id)
        ->select('do_trips.*')
        ->get();

        $data = new Collection($dataPaginator, $this->doTripTransformer);
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        $this->fractal->parseIncludes('user,user.review,user.review.reviewer,varian,trip,trip.asal,trip.asal.negara,trip.tujuan,trip.tujuan.negara,dikirimke,dikirimke.kota,dikirimke.kota.negara'); // parse includes
        $data = $this->fractal->createData($data); // Transform data

        return $data->toArray(); // Get transformed array of data
    }

    public function getFollower()
    {
        $currentUser = User::getCurrentUser();
        $followersPaginator = Follower::where('user_id', '=', $currentUser->id)->paginate(10);

        $followers = new Collection($followersPaginator->items(), $this->followerTransformer);
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        $this->fractal->parseIncludes('user,follower'); // parse includes
        $followers->setPaginator(new IlluminatePaginatorAdapter($followersPaginator));

        $followers = $this->fractal->createData($followers); // Transform data

        return $followers->toArray(); // Get transformed array of data
    }

    public function follow($user_id)
    {
        $currentUser = User::getCurrentUser();

        if(!User::find($user_id))
            return response()->json(['message' => 'User dengan id tersebut tidak ditemukan']);        

        DB::beginTransaction();
        try{
            $follow = Follower::create([
                'user_id' => $user_id,
                'follower_user_id' => $currentUser->id,
            ]);

            NotifikasiUser::newNotifikasi($user_id, $currentUser->name . ' melakukan follow ke anda');
        } catch(Exception $e)
        {
            DB::rollBack();
            return response()->json(['message' => 'Gagal untuk follow user']);        
        }

        DB::commit();
        return response()->json(['message' => 'Berhasil untuk follow user']);        
    }

    public function report(Request $request, $tipe)
    {
        $currentUser = User::getCurrentUser();
        $request->user_id = $currentUser->id;

        if(!User::find($request->user_id))
            return response()->json(['message' => 'User dengan id tersebut tidak ditemukan']);    

        if($tipe == 0)
            $data = Preorder::find($request->post_id);
        if($tipe == 1)
            $data = Requesting::find($request->post_id);
        if($tipe == 2)
            $data = Trip::find($request->post_id);

        if($data == null)
            return response()->json(['message' => 'Post Barang dengan id tersebut tidak ditemukan']);    

        // $currentUser = User::getCurrentUser();
        // if($currentUser->id == $data->user_id)
        // {
        //     DB::rollBack();   
        //     return response()->json(array('data'=>'cannot offer your owned stuff'),500);
        // }

        if(Reporting::saveReport($request, $tipe))
        {
            return response()->json(array('message'=>'Berhasil melaporkan post ini'),200);
        }
        else
        {
            return response()->json(array('message'=>'Gagal melaporkan post ini'),500);
        }
    }

    public function getReview()
    {
        $currentUser = User::getCurrentUser();
        $reviewsPaginator = Review::where('user_id', '=', $currentUser->id)->paginate(10);

        $reviews = new Collection($reviewsPaginator->items(), $this->reviewTransformer);
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        $this->fractal->parseIncludes('user,reviewer'); // parse includes
        $reviews->setPaginator(new IlluminatePaginatorAdapter($reviewsPaginator));

        $reviews = $this->fractal->createData($reviews); // Transform data

        return $reviews->toArray(); // Get transformed array of data
    }

    public function review(Request $request)
    {
        $currentUser = User::getCurrentUser();

        if($currentUser->id == $request->id_User)
            return response()->json(['message' => 'Tidak Bisa review diri sendiri']);        

        if(!User::find($request->id_User))
            return response()->json(['message' => 'User dengan id tersebut tidak ditemukan']);        

        DB::beginTransaction();
        try{
            $follow = Review::create([
                'pesan' => $request->deskripsi,
                'rating' => $request->rating,
                'user_id' => $request->id_User,
                'reviewer_user_id' => $currentUser->id
            ]);

            NotifikasiUser::newNotifikasi($follow->user_id, $currentUser->name . ' memberikan review ke anda');
        } catch(Exception $e)
        {
            DB::rollBack();
            return response()->json(['message' => 'Gagal untuk review user']);        
        }

        DB::commit();
        return response()->json(['message' => 'Berhasil untuk review user']);        
    }

    //ga dipake harusnya sekarang
    public function searchResult($kata)
    {
        // if($kata == "@all")
        // $query = DetailProduk::paginate(30); // Get users from DB
        // else
        // $query = DetailProduk::where('nama','like',"%" . $kata . "%")->paginate(30); // Get users from DB

        //filter kategori saja
        // $kategori = 28;
        // $query = $query->filter(function($value, $key) use($kategori) {
        //     return $value->kategori_id == $kategori;
        // });

        // //filter preorder saja
        // // $query = $query->filter(function($value, $key) {
        // //     return !is_null($value->preorder);
        // // });

        // //filter request saja
        // $query = $query->filter(function($value, $key) {
        //     return !is_null($value->requesting);
        // });

        // //filter kota request apa aja
        // $kota = [7,8];
        // $query = $query->filter(function($value, $key) use($kota){
        //     if(!is_null($value->requesting))
        //     {
        //         return in_array($value->requesting->dibeli_dari, $kota);
        //     }
        // });

        // //filter kota preorder apa aja
        // $kota = [7,8];
        // $query = $query->filter(function($value, $key) use($kota){
        //     if(!is_null($value->preorder))
        //     {
        //         return in_array($value->preorder->dibeli_dari, $kota);
        //     }
        // });

        //filter rating diatas 4
        // $query = $query->filter(function($value, $key){
        //     if(!is_null($value->preorder))
        //     {
        //         $rating = $value->preorder->user->review()->pluck('rating')->avg();
        //         if(is_null($rating))
        //             $rating = 0;

        //         return $rating >= 4;
        //     }
        //     else if(!is_null($value->requesting))
        //     {
        //         $rating = $value->requesting->user->review()->pluck('rating')->avg();
        //         if(is_null($rating))
        //             $rating = 0;

        //         return $rating >= 4;
        //     }
        // });

        // $query = $query->sortBy(function ($value, $key) {
        //     if(!is_null($value->preorder))
        //     {
        //         $rating = $value->preorder->user->review()->pluck('rating')->avg();
        //         if(is_null($rating))
        //             $rating = 0;

        //         return $rating;
        //     }
        //     else if(!is_null($value->requesting))
        //     {
        //         $rating = $value->requesting->user->review()->pluck('rating')->avg();
        //         if(is_null($rating))
        //             $rating = 0;

        //         return $rating;
        //     }
        // });

        // $query = $query->sortBy(function ($value, $key) {
        //     return $value->varian->first()->harga;
        // });

        // $query = $query->sortByDesc(function ($value, $key) {
        //     return $value->varian->first()->harga;
        // });

        // $pageNum = \Illuminate\Pagination\Paginator::resolveCurrentPage('searchResult');
        // $detailProdukPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
        //     $query->forPage($pageNum,30), 
        //     $query->count(),
        //     30, 
        //     $pageNum, 
        //     ['path'=>url('api/get/search_result') . '/'.$kata,'pageName' => 'searchResult',]);


        $detailProdukPaginator = DetailProduk::where('nama','like',"%" . $kata . "%")->paginate(30); // Get users from DB
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

    public function seenPost(Request $request)
    {
        $currentUser = User::getCurrentUser();
        $request->user_id = $currentUser->id;
        if($currentUser)
        {
           $data = HasSeen::addSeen($request);
        }
        else
        {
            return response()->json(['message' => 'User Belum Login']);      
        }

        if($data)
            return response()->json(['message' => 'Berhasil melihat post']);      
        else
            return response()->json(['message' => 'Gagal melihat post']);      
    }

    public function uploadPicture(Request $request)
    {
        try
        {
            $path = Gambar::savePictureToServer($request->gambar);
        } catch(\Exception $e)
        {
            return response()->json(['message' => 'Gagal upload gambar']);        
        }

        return response()->json(['message' => url($path)]);
    }

    public function changeAvatar(Request $request)
    {
        try
        {
            DB::beginTransaction();
            $currentUser = User::getCurrentUser();
            $user = User::find($currentUser->id);
            $path = Gambar::uploadAvatar($request->gambar);
            $user->avatar = $path;
            $user->save();
        } catch(\Exception $e)
        {
            DB::rollBack();
            return response()->json(['message' => 'Gagal upload gambar']);        
        }

        DB::commit();
        return response()->json(['message' => url($path)]);
    }

    public function changeRincian(Request $request)
    {
        try
        {
            DB::beginTransaction();
            $currentUser = User::getCurrentUser();
            $user = User::find($currentUser->id);
            $user->rincian = $request->rincian;
            $user->save();
        } catch(\Exception $e)
        {
            DB::rollBack();
            return response()->json(['message' => 'Gagal mengubah rincian']);        
        }

        DB::commit();
        return response()->json(['message' => 'Berhasil mengubah rincian']);
    }
}
