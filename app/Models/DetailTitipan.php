<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailTitipan extends Model
{
    protected $fillable = ['harga', 'titipan_id', 'varian_id', 'postdata_id', 'postdata_type'];

    public function titipan()
    {
        return $this->belongsTo(Titipan::class);
    }

    public function varian()
    {
        return $this->belongsTo(Varian::class);
    }

    public function postdata()
    {
        return $this->morphTo();
    }

    public function saveDetailTitipan(Request $request)
    {
        $ongkir = new \RajaOngkir(env("RAJAONGKIR_API"), true);
        $ongkirdari = collect(json_decode($ongkir->getCity()))->firstWhere('city_name', ucfirst($dari));
        $ongkirtujuan = collect(json_decode($ongkir->getCity()))->firstWhere('city_name', ucfirst($tujuan));

        $ongkir->getCost($ongkirdari->city_id, $ongkirtujuan->city_id, $berat, 'jne');
    }
}
