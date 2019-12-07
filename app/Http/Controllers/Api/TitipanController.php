<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;
use DB;
use App\User;
use App\Models\BoxTitipan;
use App\Models\DetailTitipan;
use App\Models\Titipan;
use App\Transformers\BoxTitipanTransformer;
use App\Transformers\DetailTitipanTransformer;
use App\Transformers\TitipanTransformer;

use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;


class TitipanController extends Controller
{
    private $fractal;
    private $boxTitipanTransformer;

    function __construct(Manager $fractal, BoxTitipanTransformer $boxTitipanTransformer, TitipanTransformer $titipanTransformer,DetailTitipanTransformer $detailTitipanTransformer)
    {
        $this->fractal = $fractal;
        $this->boxTitipanTransformer = $boxTitipanTransformer;
        $this->titipanTransformer = $titipanTransformer;
        $this->detailTitipanTransformer = $detailTitipanTransformer;

    }

    public function index()
    {
        $requesting = \App\Models\Requesting::find(2);
        dd($requesting->detail_produk->varian->first()->id);
        $ongkir = new \RajaOngkir(env("RAJAONGKIR_API"), true);
        // dd(collect(json_decode($ongkir->getCity()))->firstWhere('city_name', 'Surabaya'));
        $hargaKirim = $ongkir->getCost(501, 114, 1700, "jne");

        return collect(json_decode($hargaKirim))->first()->results;
    }

    public function myBox()
    {
        $currentUser = User::getCurrentUser();
        $dataPaginator = BoxTitipan::where('user_id', '=', $currentUser->id)->get();

        $data = new Collection($dataPaginator, $this->boxTitipanTransformer);
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        $this->fractal->parseIncludes('user,varian,varian.detail,varian.gambar,dikirimke,dikirimke.kota,dikirimke.kota.negara'); // parse includes
        $data = $this->fractal->createData($data); // Transform data

        return $data->toArray(); // Get transformed array of data
    }

    public function showUserTitipan()
    {
        $titipanPaginator = Titipan::where('user_id',Auth::user()->id)->orderBy('created_at','desc')->paginate(10); // Get users from DB
        $titipan = new Collection($titipanPaginator->items(), $this->titipanTransformer); // Create a resource collection transformer
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        // $this->fractal->parseIncludes("post, dikirimke, user, shopper, detail"); // parse includes
        $this->fractal->parseIncludes("shopper,detail,post"); // parse includes
        $titipan->setPaginator(new IlluminatePaginatorAdapter($titipanPaginator));
        $titipan = $this->fractal->createData($titipan); // Transform data
        return $titipan->toArray();
    }

    public function showShopperTitipan()
    {                                                                           // ini 4 soalnya 4 adalah sudah terverif dibayar
        $titipanPaginator = Titipan::where('shopper_id',Auth::user()->id)->where(function($q) {
            $q->where('status_transaksi_id', "4")
              ->orWhere('status_transaksi_id', "5");
        })->orderBy('created_at','desc')->paginate(10); // Get users from DB
        $titipan = new Collection($titipanPaginator->items(), $this->titipanTransformer); // Create a resource collection transformer
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        // $this->fractal->parseIncludes('post'); // parse includes
        $this->fractal->parseIncludes("user,detail,post");
        $titipan->setPaginator(new IlluminatePaginatorAdapter($titipanPaginator));
        $titipan = $this->fractal->createData($titipan); // Transform data
        return $titipan->toArray();
    }

    public function hitungOngkir(Request $request)
    {
        $request->validate([
            'box_titipan_id' => 'required',
            'kurir' => 'required',
        ]);

        $box_titipan_id = $request->box_titipan_id;
        $box = BoxTitipan::find($box_titipan_id);
        $dari = ucfirst($box->dikirim_dari);
        $tujuan = ucfirst($box->dikirimke()->kota()->nama_kota);
        $berat = $box->berat;

        $ongkir = new \RajaOngkir(env("RAJAONGKIR_API"), true);
        $dari = collect(json_decode($ongkir->getCity()))->firstWhere('city_name', $dari);
        $tujuan = collect(json_decode($ongkir->getCity()))->firstWhere('city_name', $tujuan);

        $hargaKirim = $ongkir->getCost($dari, $tujuan, $berat, $request->kurir);
        
        return $hargaKirim;
    }

    public function jadiTitipan(Request $request)
    {
        $currentUser = User::getCurrentUser();
        $request->user_id = $currentUser->id;

        $request->validate([
            'box_ids' => 'required',
            'total_harga_kirim' => 'required',
            'metode_bayar' => 'required',
            'kurir_id' => 'required',
        ]);

        $box_ids = explode('~@~', $request->box_ids);
        $data = BoxTitipan::find($box_ids[0]);

        $currentUser = User::getCurrentUser();
        if($currentUser->id != $data->user_id)
        {
            DB::rollBack();   
            return response()->json(array('message'=>'Box titipan bukan milik anda'),500);
        }

        $result=Titipan::saveToTitipan($request);

        if($result!=null)
        {
            
            $detailtitipan = new Item($result, $this->titipanTransformer); // Create a resource collection transformer
            
            $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
            $detailtitipan = $this->fractal->createData($detailtitipan); // Transform data
            return response()->json(array('message'=>'Berhasil melakukan titipan','data'=>$detailtitipan->toArray()),200);
        }
        else
        {
            return response()->json(array('message'=>'Gagal melakukan titipan'),500);
        }
    }

    public function updatePembayaran(Request $request)
    {
        $currentUser = User::getCurrentUser();
        $request->user_id = $currentUser->id;

        $request->validate([
            'titipan_id' => 'required',
            'bukti_gambar' => 'required',
        ]);

        $data = Titipan::find($request->titipan_id);

        $currentUser = User::getCurrentUser();
        if($currentUser->id != $data->user_id)
        {
            DB::rollBack();   
            return response()->json(array('message'=>'Titipan bukan milik anda'),500);
        }

        try{
            // $path = Gambar::savePictureToServer($request->bukti_bayar);
            // $data->bukti_bayar = $path;
            $data->status_transaksi_id = 3;
            $data->save();
        } catch(\Exception $e){
            return response()->json(array('message'=>'Gagal Melakukan Pembayaran'),500);
        }

        return response()->json(array('message'=>'Berhasil Melakukan Pembayaran'),200);
    }

    public function updatePengiriman(Request $request)
    {
        $currentUser = User::getCurrentUser();
        $request->user_id = $currentUser->id;

        $request->validate([
            'titipan_id' => 'required',
            'nomer_resi' => 'required',
        ]);

        $data = Titipan::find($request->titipan_id);

        $currentUser = User::getCurrentUser();
        if($currentUser->id != $data->shopper_id)
        {
            DB::rollBack();   
            return response()->json(array('message'=>'Titipan bukan milik anda'),500);
        }

        try{
            $data->nomer_resi = $request->nomer_resi;
            $data->status_transaksi_id = 5;
            $data->save();
        } catch(\Exception $e){
            return response()->json(array('message'=>'Gagal mengubah status pengiriman'),500);
        }

        return response()->json(array('message'=>'Berhasil mengubah status pengiriman'),200);
    }

    public function getDetailTitipan(Request $request)
    {
        
        $request->validate([
            'titipan_id' => 'required',
        ]);

        $detailTitipanPaginator = DetailTitipan::where("titipan_id",$request->titipan_id)->paginate(10);
        $detailtitipan = new Collection($detailTitipanPaginator->items(), $this->detailTitipanTransformer); // Create a resource collection transformer
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        $this->fractal->parseIncludes("detailProduct,gambarProduct");
        $detailtitipan->setPaginator(new IlluminatePaginatorAdapter($detailTitipanPaginator));
        $detailtitipan = $this->fractal->createData($detailtitipan); // Transform data
        return $detailtitipan->toArray();
    }


    public function showAdminTitipan()
    { 
        $currentUser = User::getCurrentUser();
        if($currentUser->id != "1")
        {
            DB::rollBack();   
            return response()->json(array('message'=>'Bukan Admin ya'),500);
        }
        $titipanPaginator = Titipan::where('status_transaksi_id',"3")->orderBy('created_at','desc')->paginate(10); // Get users from DB
        $titipan = new Collection($titipanPaginator->items(), $this->titipanTransformer); // Create a resource collection transformer
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        $this->fractal->parseIncludes("user,detail,post");
        $titipan->setPaginator(new IlluminatePaginatorAdapter($titipanPaginator));
        $titipan = $this->fractal->createData($titipan); // Transform data
        return $titipan->toArray();
    }

    public function confirmAdminTitipan(Request $request)
    { 
        $request->validate([
            'titipan_id' => 'required',
        ]);
        
        $data = Titipan::find($request->titipan_id);

        $currentUser = User::getCurrentUser();
        if($currentUser->id != "1")
        {
            DB::rollBack();   
            return response()->json(array('message'=>'Bukan Admin ya'),500);
        }

        try{
            $data->status_transaksi_id = 4;
            $data->save();
        } catch(\Exception $e){
            return response()->json(array('message'=>'Gagal mengubah status Pembayaran'),500);
        }

        return response()->json(array('message'=>'Berhasil mengubah status Pembayaran'),200);
    }
}
