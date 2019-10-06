<?php
namespace App\Transformers;

use App\Models\DetailProduk;
use League\Fractal\TransformerAbstract;

class DetailProdukTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'kategori', 'gambar', 'preorder', 'trip', 'requesting', 'varian'
    ];

    public function transform(DetailProduk $detil)
    {
        return [
            'detail_id'=> $detil->id, 
            'nama'=> $detil->nama, 
            'deskripsi'=> $detil->deskripsi, 
            'berat'=> $detil->berat, 
            'satuan_berat'=> $detil->satuan_berat, 
            'kategori_id'=> $detil->kategori_id,
            'created_at'=> $detil->created_at,
            'updated_at'=> $detil->updated_at,
        ];
    }

    public function includeKategori(DetailProduk $detil)
    {
        return $this->item($detil->kategori, \App::make(KategoriTransformer::class),'include');
    }

    public function includeGambar(DetailProduk $detil)
    {
        return $this->collection($detil->gambar, \App::make(GambarTransformer::class),'include');
        // return $this->primitive($detil->gambar, function ($gambar) {
        //     return $gambar;
        // });
    }

    public function includeVarian(DetailProduk $detil)
    {
        return $this->collection($detil->varian, \App::make(VarianTransformer::class),'include');
    }

    public function includePreorder(DetailProduk $detil)
    {
        if(!is_null($detil->preorder))
            return $this->item($detil->preorder, \App::make(PreorderTransformer::class),'include');
        else
            return null;
    }

    public function includeTrip(DetailProduk $detil)
    {
        if(!is_null($detil->trip))
            return $this->item($detil->trip, \App::make(TripTransformer::class),'include');
        else
            return null;
    }

    public function includeRequesting(DetailProduk $detil)
    {
        if(!is_null($detil->requesting))
            return $this->item($detil->requesting, \App::make(RequestingTransformer::class),'include');
        else
            return null;
    }
}