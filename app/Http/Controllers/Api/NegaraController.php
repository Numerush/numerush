<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Negara;
use App\Transformers\NegaraTransformer;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;


class NegaraController extends Controller
{
    private $fractal;
    private $negaraTransformer;

    function __construct(Manager $fractal, NegaraTransformer $negaraTransformer)
    {
        $this->fractal = $fractal;
        $this->negaraTransformer = $negaraTransformer;
    }

    public function index()
    {
        $negaras = Negara::all(); // Get users from DB
        $negaras = new Collection($negaras, $this->negaraTransformer); // Create a resource collection transformer
        $this->fractal->setSerializer(new \App\Foundations\Fractal\NoDataArraySerializer);
        // $this->fractal->parseIncludes('kota'); // parse includes
        $negaras = $this->fractal->createData($negaras); // Transform data

        return $negaras->toArray(); // Get transformed array of data
    }
}
