<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;
use Auth;
use App\User;
use App\Models\HasSeen;
use App\Models\BoxTitipan;
use App\Models\DoPreorder;
use App\Models\Preorder;
use App\Models\DefaultAlamat;
use App\Models\PostKurir;
use App\Models\NotifikasiUser;

use App\Transformers\PreorderTransformer;
use App\Transformers\DoPreorderTransformer;

use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;

use Carbon\Carbon;

class PreorderController extends Controller
{
    private $fractal;
    private $preorderTransformer;
    private $doPreorderTransformer;

    function __construct(Manager $fractal, PreorderTransformer $preorderTransformer
    , DoPreorderTransformer $doPreorderTransformer)
    {
        $this->fractal = $fractal;
        $this->preorderTransformer = $preorderTransformer;
        $this->doPreorderTransformer = $doPreorderTransformer;
    }

    public function index()
    {
        if(isset($_GET['search']))
        {
            $kata = $_GET['search'];
            if($kata != "")
            {
                $query = Preorder::join('detail_produks','preorders.detail_produk_id','=','detail_produks.id')
                ->join('users','preorders.user_id','=','users.id')
                ->where([['expired', '>', Carbon::now()->timestamp],
                ['detail_produks.nama', 'like', '%' . $kata . '%']])
                ->orWhere([['expired', '>', Carbon::now()->timestamp],
                ['users.name', 'like', '%' . $kata . '%']])
                ->select('preorders.*')
                ->orderBy('preorders.created_at','desc')->paginate(30); // Get users from DB
            }
            else
                $query = Preorder::where('expired', '>', Carbon::now()->timestamp)->orderBy('created_at','desc')->paginate(30); // Get users from DB
        }
        else
        {
            $query = Preorder::where('expired', '>', Carbon::now()->timestamp)->orderBy('created_at','desc')->paginate(30); // Get users from DB
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
                ['postdata_type','App\Models\Preorder'],
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
        
        $pageNum = \Illuminate\Pagination\Paginator::resolveCurrentPage('getPreorder');
        $preorderPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $query->forPage($pageNum,30), 
            $query->count(),
            30, 
            $pageNum, 
            ['path'=>url('api/get/preorder'),'pageName' => 'getPreorder',]);

        $preorder = new Collection($preorderPaginator->items(), $this->preorderTransformer); // Create a resource collection transformer
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        $this->fractal->parseIncludes('user,user.review,user.review.reviewer,detail,detail.varian,detail.kategori,detail.gambar,dibelidari,dibelidari.negara'); // parse includes
        $preorder->setPaginator(new IlluminatePaginatorAdapter($preorderPaginator));
        $preorder = $this->fractal->createData($preorder); // Transform data

        return $preorder->toArray(); // Get transformed array of data
    }

    public function myPreorder()
    {
        if(isset($_GET['search']))
        {
            $kata = $_GET['search'];
            if($kata != "")
            {
                $query = Preorder::join('detail_produks','preorders.detail_produk_id','=','detail_produks.id')
                ->join('users','preorders.user_id','=','users.id')
                ->where([['user_id',Auth::user()->id],
                ['detail_produks.nama', 'like', '%' . $kata . '%']])
                ->orWhere([['expired', '>', Carbon::now()->timestamp],
                ['users.name', 'like', '%' . $kata . '%']])
                ->select('preorders.*')
                ->orderBy('preorders.created_at','desc')->paginate(30); // Get users from DB
            }
            else
            {
                $query = Preorder::where('user_id',Auth::user()->id)->orderBy('created_at','desc')->paginate(30); // Get users from DB
            }
        }
        else
        {
            $query = Preorder::where('user_id',Auth::user()->id)->orderBy('created_at','desc')->paginate(30); // Get users from DB
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
        
        $pageNum = \Illuminate\Pagination\Paginator::resolveCurrentPage('myPreorder');
        $preorderPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $query->forPage($pageNum,30), 
            $query->count(),
            30, 
            $pageNum, 
            ['path'=>url('api/my/preorder'),'pageName' => 'myPreorder',]);        
        
        $preorder = new Collection($preorderPaginator->items(), $this->preorderTransformer); // Create a resource collection transformer
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        $this->fractal->parseIncludes('user,user.review,user.review.reviewer,detail,detail.varian,detail.kategori,detail.gambar,dibelidari,dibelidari.negara'); // parse includes
        $preorder->setPaginator(new IlluminatePaginatorAdapter($preorderPaginator));
        $preorder = $this->fractal->createData($preorder); // Transform data

        return $preorder->toArray(); // Get transformed array of data
    }

    public function myPreorderOffer()
    {
        $doPreorderPaginator = DoPreorder::where('user_id',Auth::user()->id)->orderBy('created_at','desc')->paginate(30); // Get users from DB
        $doPreorder = new Collection($doPreorderPaginator->items(), $this->doPreorderTransformer); // Create a resource collection transformer
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        $this->fractal->parseIncludes('varian,varian.detail,varian.detail.kategori,varian.detail.gambar,dikirimke,preorder'); // parse includes
        $doPreorder->setPaginator(new IlluminatePaginatorAdapter($doPreorderPaginator));
        $doPreorder = $this->fractal->createData($doPreorder); // Transform data

        return $doPreorder->toArray(); // Get transformed array of data
    }

    public function detailProduk($id)
    {
        $preorder = Preorder::where('id',$id)->get(); // Get users from DB
     
        $resource = new Collection($preorder, $this->preorderTransformer);
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        $this->fractal->parseIncludes('user,user.review,user.review.reviewer,detail,detail.varian,detail.kategori,detail.gambar,dibelidari,dibelidari.negara'); // parse includes
        $resource = $this->fractal->createData($resource); // Transform data

        return $resource->toArray(); // Get transformed array of data
    }

    public function hargaKirim($post_id)
    {
        $currentUser = User::getCurrentUser();
        $preorder = Preorder::find($post_id); // Get users from DB
        if(empty($preorder))
            return response()->json(array('message'=>'List Preorder tidak ditemuan'),500);

        $alamat = DefaultAlamat::where('user_id', $currentUser->id)->first(); // Get users from DB

        $ongkir = new \RajaOngkir(env("RAJAONGKIR_API"), true);
        $dari = $preorder->dikirim_dari;
        $tujuan = $alamat->alamat->kota_rajaongkir;

        $berat = $preorder->detail_produk->berat;
        $satuan_berat = $preorder->detail_produk->satuan_berat;

        if($satuan_berat == "kg")
            $berat = $berat * 1000;
        
        $kurirs = PostKurir::where([['postdata_id',$post_id],['postdata_type', 0]])->first();

        $dari = collect(json_decode($ongkir->getCity()))->firstWhere('city_name', $dari);
        $tujuan = collect(json_decode($ongkir->getCity()))->firstWhere('city_name', $tujuan);

        $hargaKirim = $ongkir->getCost($dari->city_id, $tujuan->city_id, $berat, $kurirs->kurir->kode);
        $hasil = json_decode($hargaKirim);
        $hasil = collect($hasil->rajaongkir->results[0]);
        return $hasil;
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
            'berat' => 'required',
            'gambar1' => 'required',

            'dibeli_dari' => 'required',
            'dikirim_dari' => 'required',
            'estimasi_pengiriman' => 'required',
            'expired' => 'required',
            'kurir_id' => 'required',
        ]);

        if(Preorder::savePreorder($request))
        {
            return response()->json(array('message'=>'Berhasil membuat preorder baru'),200);
        }
        else
        {
            return response()->json(array('message'=>'Gagal membuat preorder baru'),500);
        }
    }

    public function hitungOngkirPreorder($preorder_id)
    {
        $currentUser = User::getCurrentUser();
        $def_alamat = DefaultAlamat::where('user_id',$currentUser->id)->first();
        if(empty($def_alamat))
        {
            return response()->json(array('message'=>'Set alamat terlebih dahulu'),500);
        }

        $tujuan = $def_alamat->alamat->kota_rajaongkir;

        $preorder = Preorder::find($preorder_id);
        
        if(empty($preorder))
        {
            return response()->json(array('message'=>'List Preorder tidak ditemukan'),500);
        }
        

        $dari = $preorder->dikirim_dari;
        $berat = $preorder->detail_produk->berat;

        $kurirs = PostKurir::where([['postdata_id','=',$preorder_id],['postdata_type','=','App\Models\Preorder']])->get();

        $hasil = [];
        $hasil['kota_asal'] = $dari;
        $hasil['kota_tujuan'] = $tujuan;
        $hasil['result'] = [];
        foreach($kurirs as $kurir)
        {
            $ongkir = new \RajaOngkir(env("RAJAONGKIR_API"), true);
            $origin = collect(json_decode($ongkir->getCity()))->firstWhere('city_name', $dari);
            $destination = collect(json_decode($ongkir->getCity()))->firstWhere('city_name', $tujuan);
            
            $hargaKirim = $ongkir->getCost($origin->city_id, $destination->city_id, $berat, $kurir->kurir->kode);
            foreach(collect(json_decode($hargaKirim))->first()->results as $result)
            {
                array_push($hasil['result'], $result);
            }
        }
        return $hasil;
    }
    
    public function newUserPreorder(Request $request)
    {
        $currentUser = User::getCurrentUser();
        $request->user_id = $currentUser->id;

        $data = Preorder::find($request->preorder_id);
        $currentUser = User::getCurrentUser();
        if($currentUser->id == $data->user_id)
        {
            DB::rollBack();   
            return response()->json(array('message'=>'Tidak bisa memesan pada post milik anda sendiri'),500);
        }

        $request->validate([
            'preorder_id' => 'required',
            'dikirim_ke' => 'required',
            'jumlah' => 'required',
            'varian_id' => 'required',
            'rincian' => 'required',
        ]);

        if(DoPreorder::saveDoPreorder($request))
        {
            NotifikasiUser::newNotifikasi($data->user_id, $currentUser->name . ' melakukan preorder pada post anda');
            return response()->json(array('message'=>'Berhasil melakukan preorder'),200);
        }
        else
        {
            return response()->json(array('message'=>'Gagal melakukan preorder'),500);
        }
    }

    public function respondUserPreorder(Request $request)
    {
        $do_preorder_id = $request->do_preorder_id;
        $respond = $request->respond;//1 atau 0

        $request->validate([
            'do_preorder_id' => 'required',
            'respond' => 'required|boolean',
        ]);

        $data = DoPreorder::find($do_preorder_id);
        $currentUser = User::getCurrentUser();
        if($currentUser->id != $data->preorder->user_id)
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
                BoxTitipan::saveToBox($do_preorder_id, 0);
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
        NotifikasiUser::newNotifikasi($data->user_id, $currentUser->name . ' menerima preorder anda pada post-nya');
        return response()->json(array('message'=>'Berhasil memberikan respon'),200);
    }
}
