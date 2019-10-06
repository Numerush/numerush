<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;
use App\Models\Kurir;
use App\Models\PostKurir;
use App\Models\DefaultKurir;

use App\Transformers\KurirTransformer;
use App\Transformers\PostKurirTransformer;
use App\Transformers\DefaultKurirTransformer;

use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;

use Illuminate\Support\Facades\DB;

class KurirController extends Controller
{
    private $fractal;
    private $kurirTransformer;
    private $defaultKurirTransformer;
    private $postKurirTransformer;

    function __construct(Manager $fractal, KurirTransformer $kurirTransformer
        , DefaultKurirTransformer $defaultKurirTransformer
        , PostKurirTransformer $postKurirTransformer)
    {
        $this->fractal = $fractal;
        $this->kurirTransformer = $kurirTransformer;
        $this->defaultKurirTransformer = $defaultKurirTransformer;
        $this->postKurirTransformer = $postKurirTransformer;
    }

    public function getKurir()
    {
        $kurirs = Kurir::all(); // Get users from DB
        $kurirs = new Collection($kurirs, $this->kurirTransformer); // Create a resource collection transformer
        $kurirs = $this->fractal->createData($kurirs); // Transform data

        return $kurirs->toArray(); // Get transformed array of data
    }

    public function getDefaultKurir()
    {
        $currentUser = User::getCurrentUser();
        $kurirs = DefaultKurir::where('user_id', $currentUser->id)->get(); // Get users from DB
        $kurirs = new Collection($kurirs, $this->defaultKurirTransformer); // Create a resource collection transformer
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        $this->fractal->parseIncludes('kurir'); // parse includes
        $kurirs = $this->fractal->createData($kurirs); // Transform data

        return $kurirs->toArray(); // Get transformed array of data
    }

    public function setDefaultKurir(Request $request)
    {
        $currentUser = User::getCurrentUser();
        $request->user_id = $currentUser->id;

        DB::beginTransaction();
        try{
            $kurir = DefaultKurir::addDefault($request);

            if(!$kurir)
                throw new Exception('Gagal tambah default');
        } catch(Exception $e)
        {
            DB::rollBack();
            return response()->json(['message' => 'Gagal untuk tambah default kurir']);        
        }

        DB::commit();
        return response()->json(['message' => 'Berhasil untuk tambah default kurir']);       
    }

    public function getPostKurir($post_id, $tipe)
    {
        if($tipe == 0)
            $tipepost = 'App\Models\Preorder';
        else if($tipe == 1)
            $tipepost = 'App\Models\DoRequesting';
        else if($tipe == 2)
            $tipepost = 'App\Models\Trip';

        $kurirs = PostKurir::where([['postdata_id',$post_id],['postdata_type',$tipepost]])->get(); // Get users from DB
        $kurirs = new Collection($kurirs, $this->postKurirTransformer); // Create a resource collection transformer
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        $this->fractal->parseIncludes('kurir'); // parse includes
        $kurirs = $this->fractal->createData($kurirs); // Transform data

        if(empty($kurirs->toArray()['data']))
        {
            $array = $this->getKurir();
        }
        else
            $array = $kurirs->toArray();

        return $array;
    }
}
