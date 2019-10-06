<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Kota;
use App\Transformers\KotaTransformer;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class KotaController extends Controller
{
    private $fractal;
    private $kotaTransformer;

    function __construct(Manager $fractal, KotaTransformer $kotaTransformer)
    {
        $this->fractal = $fractal;
        $this->kotaTransformer = $kotaTransformer;
    }

    public function index()
    {
        $kotas = Kota::all(); // Get users from DB
        $kotas = new Collection($kotas, $this->kotaTransformer); // Create a resource collection transformer
        // $this->fractal->parseIncludes($request->get('include', '')); // parse includes
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        $this->fractal->parseIncludes('negara'); // parse includes
        $kotas = $this->fractal->createData($kotas); // Transform data

        return $kotas->toArray(); // Get transformed array of data
    }

    public function getSearchKota($kota)
    {
        $kotas = Kota::where('nama_kota','like', '%' . $kota . '%')->get(); // Get users from DB
        $kotas = new Collection($kotas, $this->kotaTransformer); // Create a resource collection transformer
        // $this->fractal->parseIncludes($request->get('include', '')); // parse includes
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        $this->fractal->parseIncludes('negara'); // parse includes
        $kotas = $this->fractal->createData($kotas); // Transform data

        return $kotas->toArray(); // Get transformed array of data
    }

    public function getKotaFromNegara($negara)
    {
        $kotas = Kota::where('negara_id','=', $negara)->get(); // Get users from DB
        $kotas = new Collection($kotas, $this->kotaTransformer); // Create a resource collection transformer
        // $this->fractal->parseIncludes($request->get('include', '')); // parse includes
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        $this->fractal->parseIncludes('negara'); // parse includes
        $kotas = $this->fractal->createData($kotas); // Transform data

        return $kotas->toArray(); // Get transformed array of data
    }

    public function getKotaRajaOngkir()
    {
        $ongkir = new \RajaOngkir(env("RAJAONGKIR_API"), true);
        return response()->json(collect(json_decode($ongkir->getCity())));
    }
}
