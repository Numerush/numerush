<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;
use Auth;
use App\User;
use App\Models\HasSeen;
use App\Models\DetailProduk;
use App\Models\BoxTitipan;
use App\Models\DoRequesting;
use App\Models\Requesting;
use App\Models\NotifikasiUser;

use App\Transformers\RequestingTransformer;
use App\Transformers\DoRequestingTransformer;

use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;

use Carbon\Carbon;

class RequestingController extends Controller
{
    private $fractal;
    private $requestingTransformer;
    private $doRequestingTransformer;

    function __construct(Manager $fractal, DoRequestingTransformer $doRequestingTransformer
    , RequestingTransformer $requestingTransformer)
    {
        $this->fractal = $fractal;
        $this->requestingTransformer = $requestingTransformer;
        $this->doRequestingTransformer = $doRequestingTransformer;
    }

    public function index()
    {
        if(isset($_GET['search']))
        {
            $kata = $_GET['search'];
            if($kata != "")
            {
                $query = Requesting::join('detail_produks','requestings.detail_produk_id','=','detail_produks.id')
                ->join('users','requestings.user_id','=','users.id')
                ->where([['expired', '>', Carbon::now()->timestamp],
                ['detail_produks.nama', 'like', '%' . $kata . '%']])
                ->orWhere([['expired', '>', Carbon::now()->timestamp],
                ['users.name', 'like', '%' . $kata . '%']])
                ->select('requestings.*')
                ->orderBy('requestings.created_at','desc')->paginate(30); // Get users from DB
            }
            else
            {
                $query = Requesting::where('expired', '>', Carbon::now()->timestamp)->orderBy('created_at','desc')->paginate(30); // Get users from DB
            }
        }
        else
        {
            $query = Requesting::where('expired', '>', Carbon::now()->timestamp)->orderBy('created_at','desc')->paginate(30); // Get users from DB
        }

        //filter kategori saja
        if(isset($_GET['kategori']))
        {
            $kategori = $_GET['kategori'];
            if($kategori != "")
            {
                $kategori = explode('~@~', $kategori);
                $query = $query->filter(function($value, $key) use($kategori) {
                    return in_array($value->detail_produk->kategori_id, $kategori);;
                });
            }
        }

        //filter kota saja
        if(isset($_GET['kota']))
        {
            $kota = $_GET['kota'];
            if($kota != "")
            {
                $kota = explode('~@~', $kota);
                $query = $query->filter(function($value, $key) use($kota) {
                    return in_array($value->dibeli_dari, $kota);;
                });
            }
        }

        //filter rating 4 >
        if(isset($_GET['rating']))
        {
            $target = $_GET['rating'];
            if($target != "")
            {
                $query = $query->filter(function($value, $key) use($target){
                    $rating = $value->user->review()->pluck('rating')->avg();
                    if(is_null($rating))
                        $rating = 0;
    
                    return $rating >= $target;
                });
            }
        }

        //filter harga diantara
        if(isset($_GET['harga']))
        {
            $harga = $_GET['harga'];
            if($harga != "")
            {
                $harga = explode('~@~', $harga);
                $query = $query->filter(function($value, $key) use($harga){
                    return $value->detail_produk->varian->first()->harga >= $harga[0] && $value->detail_produk->varian->first()->harga <= $harga[1];
                });
            }
        }

        if(isset($_GET['sort']))
        {
            $whatSort = $_GET['sort'];

            if($whatSort == 'rating')
            {
                $query = $query->sortBy(function ($value, $key) {
                    $rating = $value->user->review()->pluck('rating')->avg();
                    if(is_null($rating))
                        $rating = 0;
    
                    return $rating;               
                });
            }
            else if($whatSort == "hargaASC")
            {
                $query = $query->sortBy(function ($value, $key) {
                    return $value->detail_produk->varian->first()->harga;
                });
            }
            else if($whatSort == "hargaDESC")
            {
                $query = $query->sortByDesc(function ($value, $key) {
                    return $value->detail_produk->varian->first()->harga;
                });
            }
        }

        $user = User::getCurrentUser();
        if($user){
            $query = $query->sortBy(function ($value, $key) use($user) {
                $data = HasSeen::where([['postdata_id',$value->id],
                ['postdata_type','App\Models\Requesting'],
                ['user_id',$user->id]])->first();
                
                if($data)
                {
                    $seen = 1;
                }
                else
                {
                    $seen = 0;
                }
                return $seen;
            });
        }
        
        $pageNum = \Illuminate\Pagination\Paginator::resolveCurrentPage('getRequesting');
        $requestingPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $query->forPage($pageNum,30), 
            $query->count(),
            30, 
            $pageNum, 
            ['path'=>url('api/get/requesting'),'pageName' => 'getRequesting',]);
        
        $requesting = new Collection($requestingPaginator->items(), $this->requestingTransformer); // Create a resource collection transformer
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        $this->fractal->parseIncludes('user,user.review,user.review.reviewer,detail,detail.varian,detail.kategori,detail.gambar,dibelidari,dibelidari.negara,dikirimke,dikirimke.kota,dikirimke.kota.negara'); // parse includes
        $requesting->setPaginator(new IlluminatePaginatorAdapter($requestingPaginator));
        $requesting = $this->fractal->createData($requesting); // Transform data

        // $requesting = collect($requesting->toArray());
        // dd($requesting);
        return $requesting->toArray(); // Get transformed array of data
    }

    //get semua requesting milik user
    public function myRequesting()
    {
        if(isset($_GET['sort']))
        {
            $sort = $_GET['sort'];        
            if($sort == "mahal"){
                $requestingPaginator = Requesting::join('detail_produks','requestings.detail_produk_id','=','detail_produks.id')
                ->join('varians','detail_produks.id', '=','varians.detail_produk_id')
                ->where('user_id',Auth::user()->id)
                ->select('requestings.*', 'varians.harga')
                ->orderBy('varians.harga','desc')
                ->paginate(10);                
            }
            elseif($sort == "murah"){
                $requestingPaginator = Requesting::join('detail_produks','requestings.detail_produk_id','=','detail_produks.id')
                ->join('varians','detail_produks.id', '=','varians.detail_produk_id')
                ->where('user_id',Auth::user()->id)
                ->select('requestings.*', 'varians.harga')
                ->orderBy('varians.harga','asc')
                ->paginate(10);
            }
            else
            {
                $requestingPaginator = Requesting::where('user_id',Auth::user()->id)->orderBy('created_at', 'desc')->paginate(30); // Get users from DB
            }
        }
        else
        {
            $requestingPaginator = Requesting::where('user_id',Auth::user()->id)->orderBy('created_at', 'desc')->paginate(30); // Get users from DB
        }
        
        $requesting = new Collection($requestingPaginator->items(), $this->requestingTransformer); // Create a resource collection transformer
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        $this->fractal->parseIncludes('user,user.review,user.review.reviewer,detail,detail.varian,detail.kategori,detail.gambar,dibelidari,dibelidari.negara,dikirimke,dikirimke.kota,dikirimke.kota.negara'); // parse includes
        $requesting->setPaginator(new IlluminatePaginatorAdapter($requestingPaginator));
        $requesting = $this->fractal->createData($requesting); // Transform data

        return $requesting->toArray(); // Get transformed array of data
    }

    public function myRequestingOffer()
    {
        $doRequestingPaginator = DoRequesting::where('user_id',Auth::user()->id)->orderBy('created_at', 'desc')->paginate(30); // Get users from DB
        
        $doRequesting = new Collection($doRequestingPaginator->items(), $this->doRequestingTransformer); // Create a resource collection transformer
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        $this->fractal->parseIncludes('varian,varian.detail,varian.detail.kategori,varian.detail.gambar,dibelidari,dibelidari.negara,requesting'); // parse includes
        $doRequesting->setPaginator(new IlluminatePaginatorAdapter($doRequestingPaginator));
        $doRequesting = $this->fractal->createData($doRequesting); // Transform data

        return $doRequesting->toArray(); // Get transformed array of data
    }

    public function detailProduk($id)
    {
        $requesting = Requesting::where('id',$id)->get(); // Get users from DB
     
        $resource = new Collection($requesting, $this->requestingTransformer);
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        $this->fractal->parseIncludes('user,user.review,user.review.reviewer,detail,detail.varian,detail.kategori,detail.gambar,dibelidari,dibelidari.negara,dikirimke,dikirimke.kota,dikirimke.kota.negara'); // parse includes
        $resource = $this->fractal->createData($resource); // Transform data

        return $resource->toArray(); // Get transformed array of data
    }

    public function add(Request $request)
    {
        $currentUser = User::getCurrentUser();
        $request->user_id = $currentUser->id;

        $request->validate([
            'nama' => 'required',
            'harga' => 'required',
            'deskripsi' => 'required',
            'kategori_id' => 'required',
            'gambar1' => 'required',

            'jumlah' => 'required',
            'dibeli_dari' => 'required',
            'dikirim_ke' => 'required',
            'expired' => 'required',
        ]);

        if(Requesting::saveRequesting($request))
        {
            return response()->json(array('message'=>'Berhasil menambahkan request baru'),200);
        }
        else
        {
            return response()->json(array('message'=>'Gagal menambahkan request baru'),500);
        }
    }

    public function newUserRequest(Request $request)
    {
        $currentUser = User::getCurrentUser();
        $request->user_id = $currentUser->id;

        $data = Requesting::find($request->requesting_id);
        $currentUser = User::getCurrentUser();
        if($currentUser->id == $data->user_id)
        {
            DB::rollBack();   
            return response()->json(array('message'=>'Tidak bisa memesan pada post milik anda sendiri'),500);
        }

        $request->validate([
            'requesting_id' => 'required',
            'berat' => 'required',
            'dibeli_dari' => 'required',
            'dikirim_dari' => 'required',
            'estimasi_pengiriman' => 'required',
            //sudah ga perlu karena disimpen di detil
            // 'varian_id' => 'required',
            'rincian' => 'required',
            'expired' => 'required',
            'kurir_id' => 'required',
        ]);

        if(DoRequesting::saveDoRequesting($request))
        {
            NotifikasiUser::newNotifikasi($data->user_id, $currentUser->name . ' melakukan request pada post anda');
            return response()->json(array('message'=>'Berhasil melakukan request barang'),200);
        }
        else
        {
            return response()->json(array('message'=>'Gagal melakukan request barang'),500);
        }
    }

    public function respondUserRequest(Request $request)
    {
        $do_requesting_id = $request->do_requesting_id;
        $respond = $request->respond;//1 atau 0

        $request->validate([
            'do_requesting_id' => 'required',
            'respond' => 'required|boolean',
        ]);
        
        $data = DoRequesting::find($do_requesting_id);
        $currentUser = User::getCurrentUser();
        if($currentUser->id != $data->requesting->user_id)
        {
            DB::rollBack();   
            return response()->json(array('message'=>'Tidak dapat memberi respon terhadap barang yang bukan milikmu'),500);
        }

        try
        {
            DB::beginTransaction();
            if($respond == 1)
            {
                $data->status = 'terima';
                // $detil = DetailProduk::find($data->varian->detail_produk->id);
                // $detil->berat = $request->berat;
                // $detil->satuan_berat = $request->satuan_berat;
                // $detil->save();
                DoRequesting::rejectAllExcept($do_requesting_id, $data->requesting_id);
                BoxTitipan::saveToBox($do_requesting_id, 1);
            }
            else
            {
                $data->status = 'tolak';
            }
            $data->save();
        } catch(Exception $e)
        {
            DB::rollBack();   
            return response()->json(array('message'=>'Gagal memberikan respon'),500);
        }

        DB::commit();
        NotifikasiUser::newNotifikasi($data->user_id, $currentUser->name . ' menerima pesanan anda pada post-nya');
        return response()->json(array('message'=>'Berhasil memberikan respon'),200);
    }
}
