<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\StatusTransaksi;

class StatusController extends Controller
{
    public function index()
    {
        $status = StatusTransaksi::all();
        return view('auth.crud.status', compact('status'));
    }
}
