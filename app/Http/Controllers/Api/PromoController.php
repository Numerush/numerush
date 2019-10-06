<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Promo;
use App\Transformers\PromoTransformer;

use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;

class PromoController extends Controller
{
    private $fractal;
    private $promoTransformer;

    function __construct(Manager $fractal, PromoTransformer $promoTransformer)
    {
        $this->fractal = $fractal;
        $this->promoTransformer = $promoTransformer;
    }

    public function get3New()
    {
        $paginator = Promo::orderBy('created_at','desc')->take(3)->get(); // Get users from DB
        $data = new Collection($paginator, $this->promoTransformer); // Create a resource collection transformer
        $data = $this->fractal->createData($data); // Transform data

        return $data->toArray(); // Get transformed array of data
    }
}
