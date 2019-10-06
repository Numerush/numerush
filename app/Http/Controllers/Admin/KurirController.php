<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Kurir;

class KurirController extends Controller
{
    public function index()
    {
        $kurirs = Kurir::all();
        return view('auth.crud.kurir', compact('kurirs'));
    }
}
