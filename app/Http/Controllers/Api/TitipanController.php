<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;
use App\User;
use App\Models\BoxTitipan;
use App\Models\Titipan;
use App\Transformers\BoxTitipanTransformer;

use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;


class TitipanController extends Controller
{
    private $fractal;
    private $boxTitipanTransformer;

    function __construct(Manager $fractal, BoxTitipanTransformer $boxTitipanTransformer)
    {
        $this->fractal = $fractal;
        $this->boxTitipanTransformer = $boxTitipanTransformer;
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
        $this->fractal->parseIncludes('user,user.review,user.review.reviewer,varian,varian.detail,dikirimke,dikirimke.kota,dikirimke.kota.negara'); // parse includes
        $data = $this->fractal->createData($data); // Transform data

        return $data->toArray(); // Get transformed array of data
    }

    public function showUserTitipan()
    {
        $titipanPaginator = Titipan::where('user_id',Auth::user()->id)->orderBy('created_at','desc')->paginate(10); // Get users from DB
        $titipan = new Collection($titipanPaginator->items(), $this->titipanTransformer); // Create a resource collection transformer
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        
        $titipan->setPaginator(new IlluminatePaginatorAdapter($titipanPaginator));
        $titipan = $this->fractal->createData($titipan); // Transform data
        return $titipan->toArray();
    }

    public function showShopperTitipan()
    {
        $titipanPaginator = Titipan::where('shopper_id',Auth::user()->id)->orderBy('created_at','desc')->paginate(10); // Get users from DB
        $titipan = new Collection($titipanPaginator->items(), $this->titipanTransformer); // Create a resource collection transformer
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);

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

        if(Titipan::saveToTitipan($request))
        {
            return response()->json(array('message'=>'Berhasil melakukan titipan'),200);
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
            $path = Gambar::savePictureToServer($request->bukti_bayar);
            $titipan->bukti_bayar = $path;
            $titipan->status_transaksi_id = 3;
            $titipan->save();
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
            $titipan->nomer_resi = $request->nomer_resi;
            $titipan->status_transaksi_id = 5;
            $titipan->save();
        } catch(\Exception $e){
            return response()->json(array('message'=>'Gagal mengubah status pengiriman'),500);
        }

        return response()->json(array('message'=>'Berhasil mengubah status pengiriman'),200);
    }
}
