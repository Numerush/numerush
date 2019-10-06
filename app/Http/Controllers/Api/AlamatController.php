<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;
use App\User;
use App\Models\Alamat;
use App\Models\DefaultAlamat;
use App\Transformers\AlamatTransformer;
use App\Transformers\DefaultAlamatTransformer;

use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;

class AlamatController extends Controller
{
    private $fractal;
    private $alamatTransformer;
    private $defaultAlamatTransformer;

    function __construct(Manager $fractal, AlamatTransformer $alamatTransformer
    , DefaultAlamatTransformer $defaultAlamatTransformer)
    {
        $this->fractal = $fractal;
        $this->alamatTransformer = $alamatTransformer;
        $this->defaultAlamatTransformer = $defaultAlamatTransformer;
    }

    public function index()
    {
        $alamats = Alamat::where('user_id', Auth::user()->id)->get(); // Get users from DB
        $alamats = new Collection($alamats, $this->alamatTransformer); // Create a resource collection transformer
        // $this->fractal->parseIncludes($request->get('include', '')); // parse includes
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        $alamats = $this->fractal->createData($alamats); // Transform data

        return $alamats->toArray(); // Get transformed array of data
    }

    public function add(Request $request)
    {
        $currentUser = User::getCurrentUser();
        $request->user_id = $currentUser->id;

        if(Alamat::saveAlamat($request))
        {
            return response()->json(array('message'=>'Berhasil menambahkan alamat'),200);
        }
        else
        {
            return response()->json(array('message'=>'Gagal menambahkan alamat'),500);
        }
    }

    public function getDefaultAlamat()
    {
        $currentUser = User::getCurrentUser();
        $defaultAlamatsPaginator = DefaultAlamat::where('user_id', '=', $currentUser->id)->get();

        $defaultAlamats = new Collection($defaultAlamatsPaginator, $this->defaultAlamatTransformer);
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        $this->fractal->parseIncludes('alamat'); // parse includes

        $defaultAlamats = $this->fractal->createData($defaultAlamats); // Transform data

        return $defaultAlamats->toArray(); // Get transformed array of data
    }

    public function setDefaultAlamat(Request $request)
    {
        $currentUser = User::getCurrentUser();
        $request->user_id = $currentUser->id;

        if(DefaultAlamat::changeDefault($request))
        {
            return response()->json(array('message'=>'Berhasil mengubah default alamat'),200);
        }
        else
        {
            return response()->json(array('message'=>'Gagal mengubah default alamat'),500);
        }
    }

    public function deleteAlamat(Request $request)
    {
        $currentUser = User::getCurrentUser();
        $request->user_id = $currentUser->id;

        if(Alamat::deleteAlamat($request))
        {
            return response()->json(array('message'=>'Berhasil menghapus alamat'),200);
        }
        else
        {
            return response()->json(array('message'=>'Gagal menghapus alamat'),500);
        }
    }
}
