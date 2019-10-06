<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Negara;

class NegaraController extends Controller
{
    public function index()
    {
        $negaras = Negara::all();
        return view('auth.crud.negara', compact('negaras'));
    }
}
