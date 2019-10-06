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
use App\Models\Trip;
use App\Models\DoTrip;
use App\Models\NotifikasiUser;

use App\Transformers\TripTransformer;
use App\Transformers\DoTripTransformer;

use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;

use Carbon\Carbon;

class TripController extends Controller
{
    private $fractal;
    private $tripTransformer;
    private $doTripTransformer;

    function __construct(Manager $fractal, TripTransformer $tripTransformer
    , DoTripTransformer $doTripTransformer)
    {
        $this->fractal = $fractal;
        $this->tripTransformer = $tripTransformer;
        $this->doTripTransformer = $doTripTransformer;
    }

    public function index()
    {
        if(isset($_GET['search']))
        {
            $kata = $_GET['search'];
            if($kata != "")
            {
                $query = Trip::join('users','trips.user_id','=','users.id')
                ->where([['expired', '>', Carbon::now()->timestamp],
                ['trips.rincian', 'like', '%' . $kata . '%']])
                ->orWhere([['expired', '>', Carbon::now()->timestamp],
                ['users.name', 'like', '%' . $kata . '%']])
                ->select('trips.*')
                ->orderBy('trips.created_at','desc')->paginate(30); // Get users from DB
            }
            else
            {
                $query = Trip::where('expired', '>', Carbon::now()->timestamp)->orderBy('created_at','desc')->paginate(30); // Get users from DB
            }
        }
        else
        {
            $query = Trip::where('expired', '>', Carbon::now()->timestamp)->orderBy('created_at','desc')->paginate(30); // Get users from DB
        }

        //filter kota saja
        if(isset($_GET['kota']))
        {
            $kota = $_GET['kota'];
            if($kota != "")
            {
                $kota = explode('~@~', $kota);
                $query = $query->filter(function($value, $key) use($kota) {
                    return in_array($value->kota_asal, $kota) || in_array($value->kota_tujuan, $kota);
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
            else if($whatSort == "berangkat_terdekat")
            {
                $query = $query->sortBy(function ($value, $key) {
                    return $value->tanggal_berangkat;
                });
            }
            else if($whatSort == "berangkat_terjauh")
            {
                $query = $query->sortByDesc(function ($value, $key) {
                    return $value->tanggal_berangkat;
                });
            }
            else if($whatSort == "kembali_terdekat")
            {
                $query = $query->sortBy(function ($value, $key) {
                    return $value->tanggal_kembali;
                });
            }
            else if($whatSort == "kembali_terjauh")
            {
                $query = $query->sortByDesc(function ($value, $key) {
                    return $value->tanggal_kembali;
                });
            }
            else if($whatSort == "asal_az")
            {
                $query = $query->sortBy(function ($value, $key) {
                    return $value->asal->nama_kota;
                });
            }
            else if($whatSort == "asal_za")
            {
                $query = $query->sortByDesc(function ($value, $key) {
                    return $value->asal->nama_kota;
                });
            }
            else if($whatSort == "tujuan_az")
            {
                $query = $query->sortBy(function ($value, $key) {
                    return $value->tujuan->nama_kota;
                });
            }
            else if($whatSort == "tujuan_za")
            {
                $query = $query->sortByDesc(function ($value, $key) {
                    return $value->tujuan->nama_kota;
                });
            }
        }
        
        $pageNum = \Illuminate\Pagination\Paginator::resolveCurrentPage('getTrip');
        $tripPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $query->forPage($pageNum,30), 
            $query->count(),
            30, 
            $pageNum, 
            ['path'=>url('api/get/trip'),'pageName' => 'getTrip',]);
        
        $trip = new Collection($tripPaginator->items(), $this->tripTransformer); // Create a resource collection transformer
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        $this->fractal->parseIncludes('user,user.review,user.review.reviewer,asal,tujuan,asal.negara,tujuan.negara'); // parse includes
        $trip->setPaginator(new IlluminatePaginatorAdapter($tripPaginator));
        $trip = $this->fractal->createData($trip); // Transform data

        return $trip->toArray(); // Get transformed array of data
    }

    public function myTrip()
    {
        
        $query = Trip::where('user_id',Auth::user()->id)->orderBy('created_at','desc')->paginate(30); // Get users from DB

        //filter kota saja
        if(isset($_GET['kota']))
        {
            $kota = $_GET['kota'];
            if($kota != "")
            {
                $kota = explode('~@~', $kota);
                $query = $query->filter(function($value, $key) use($kota) {
                    return in_array($value->kota_asal, $kota) || in_array($value->kota_tujuan, $kota);
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
            else if($whatSort == "berangkat_terdekat")
            {
                $query = $query->sortBy(function ($value, $key) {
                    return $value->tanggal_berangkat;
                });
            }
            else if($whatSort == "berangkat_terjauh")
            {
                $query = $query->sortByDesc(function ($value, $key) {
                    return $value->tanggal_berangkat;
                });
            }
            else if($whatSort == "kembali_terdekat")
            {
                $query = $query->sortBy(function ($value, $key) {
                    return $value->tanggal_kembali;
                });
            }
            else if($whatSort == "kembali_terjauh")
            {
                $query = $query->sortByDesc(function ($value, $key) {
                    return $value->tanggal_kembali;
                });
            }
            else if($whatSort == "asal_az")
            {
                $query = $query->sortBy(function ($value, $key) {
                    return $value->asal->nama_kota;
                });
            }
            else if($whatSort == "asal_za")
            {
                $query = $query->sortByDesc(function ($value, $key) {
                    return $value->asal->nama_kota;
                });
            }
            else if($whatSort == "tujuan_az")
            {
                $query = $query->sortBy(function ($value, $key) {
                    return $value->tujuan->nama_kota;
                });
            }
            else if($whatSort == "tujuan_za")
            {
                $query = $query->sortByDesc(function ($value, $key) {
                    return $value->tujuan->nama_kota;
                });
            }
        }

        $user = User::getCurrentUser();
        if($user){
            $query = $query->sortBy(function ($value, $key) use($user) {
                $data = HasSeen::where([['postdata_id',$value->id],
                ['postdata_type','App\Models\Trip'],
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
        
        $pageNum = \Illuminate\Pagination\Paginator::resolveCurrentPage('myTrip');
        $tripPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $query->forPage($pageNum,30), 
            $query->count(),
            30, 
            $pageNum, 
            ['path'=>url('api/my/trip'),'pageName' => 'myTrip',]);
        
        $trip = new Collection($tripPaginator->items(), $this->tripTransformer); // Create a resource collection transformer
        $this->fractal->parseIncludes('user,user.review,user.review.reviewer,asal,tujuan,asal.negara,tujuan.negara'); // parse includes
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        $trip->setPaginator(new IlluminatePaginatorAdapter($tripPaginator));
        $trip = $this->fractal->createData($trip); // Transform data

        return $trip->toArray(); // Get transformed array of data
    }

    public function myTripOffer()
    {
        $doTripPaginator = DoTrip::where('user_id',Auth::user()->id)->orderBy('created_at', 'desc')->paginate(30); // Get users from DB
        
        $doTrip = new Collection($doTripPaginator->items(), $this->doTripTransformer); // Create a resource collection transformer
        $this->fractal->parseIncludes('varian,varian.detail,varian.detail.kategori,varian.detail.gambar,dikirimke,trip'); // parse includes
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        $doTrip->setPaginator(new IlluminatePaginatorAdapter($doTripPaginator));
        $doTrip = $this->fractal->createData($doTrip); // Transform data

        return $doTrip->toArray(); // Get transformed array of data
    }

    public function detail($id)
    {
        $trip = Trip::where('id',$id)->get(); // Get users from DB
     
        $resource = new Collection($trip, $this->tripTransformer);
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        $this->fractal->parseIncludes('user,user.review,user.review.reviewer,asal,tujuan,asal.negara,tujuan.negara'); // parse includes
        $resource = $this->fractal->createData($resource); // Transform data

        return $resource->toArray(); // Get transformed array of data
    }

    public function add(Request $request)
    {
        $currentUser = User::getCurrentUser();
        $request->user_id = $currentUser->id;

        $request->validate([
            'kota_asal' => 'required',
            'kota_tujuan' => 'required',
            'tanggal_berangkat' => 'required',
            'tanggal_kembali' => 'required',
            'rincian' => 'required',
            'estimasi_pengiriman' => 'required',
            'dikirim_dari' => 'required',
            'expired' => 'required',
            'kurir_id' => 'required',
        ]);

        if(Trip::saveTrip($request))
        {
            return response()->json(array('message'=>'Berhasil membuat trip'),200);
        }
        else
        {
            return response()->json(array('message'=>'Gagal membuat trip'),500);
        }

    }

    public function newUserTrip(Request $request)
    {
        $currentUser = User::getCurrentUser();
        $request->user_id = $currentUser->id;

        $data = Trip::find($request->trip_id);
        $currentUser = User::getCurrentUser();
        if($currentUser->id == $data->user_id)
        {
            DB::rollBack();   
            return response()->json(array('message'=>'Tidak bisa memesan pada post milik anda sendiri'),500);
        }

        $request->validate([
            'nama' => 'required',
            'harga' => 'required',
            'deskripsi' => 'required',
            'berat' => 'required',
            'gambar1' => 'required',
            'kategori_id' => 'required',

            'trip_id' => 'required',
            'jumlah' => 'required',
            'dikirim_ke' => 'required',            
            'expired' => 'required',
        ]);

        if(DoTrip::saveDoTrip($request))
        {
            NotifikasiUser::newNotifikasi($data->user_id, $currentUser->name . ' melakukan request pada trip anda');
            return response()->json(array('message'=>'Berhasil melakukan request barang pada trip ini'),200);
        }
        else
        {
            return response()->json(array('message'=>'Gagal melakukan request barang pada trip ini'),500);
        }
    }

    public function respondUserTrip(Request $request)
    {
        $do_trip_id = $request->do_trip_id;
        $respond = $request->respond;//1 atau 0

        $request->validate([
            'do_trip_id' => 'required',
            'respond' => 'required|boolean',
            'harga' => 'required',
            'berat' => 'required',
            'satuan_berat' => 'required',
        ]);

        $data = DoTrip::find($do_trip_id);
        $currentUser = User::getCurrentUser();
        if($currentUser->id != $data->trip->user_id)
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
                $detil = DetailProduk::find($data->varian->detail_produk->id);
                $detil->varian->first()->harga = $request->harga;
                $detil->berat = $request->berat;
                $detil->satuan_berat = $request->satuan_berat;
                $detil->save();
                BoxTitipan::saveToBox($do_trip_id, 2);
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
        NotifikasiUser::newNotifikasi($data->user_id, $currentUser->name . ' menerima request anda pada post-nya');
        return response()->json(array('message'=>'Berhasil memberikan respon'),200);
    }
}
