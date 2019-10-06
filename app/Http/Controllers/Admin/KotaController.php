<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Kota;

class KotaController extends Controller
{
    public function index()
    {
        $kotas = Kota::all();
        return view('auth.crud.kota', compact('kotas'));
    }
}
