<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Titipan;

class TransactionController extends Controller
{
    public function index()
    {
        $titipans = Titipan::where('status_transaksi_id','=',3)->get();
        return view('auth.transaction.index', compact('titipans'));
    }

    public function verified(Request $request)
    {
        $request->validate([
            'titipan_id' => 'required',
        ]);

        DB::beginTransaction();
        try
        {
            $titipan = Titipan::find($request->titipan_id);
            $titipan->status_transaksi_id = 4;
            $titipan->save();
        } catch (\Exception $e)
        {
            DB::rollBack();
            return redirect()->back()->with('error','Gagal Melakukan Verifikasi Transaksi');
        }

        DB::commit();
        return redirect()->back()->with('error','Berhasil Melakukan Verifikasi Transaksi');
    }
}
