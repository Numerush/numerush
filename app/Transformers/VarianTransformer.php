<?php
namespace App\Transformers;

use App\Models\Gambar;
use App\Models\Varian;
use League\Fractal\TransformerAbstract;

class VarianTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'detail',
        'gambar'
    ];

    public function transform(Varian $varian)
    {
        return [
            'id' => $varian->id,
            'nama' => $varian->nama,
            'harga' => $varian->harga,
        ];
    }

    public function includeDetail(Varian $varian)
    {
        return $this->item($varian->detail_produk, \App::make(DetailProdukTransformer::class), 'include');
    }

    public function includeGambar(Varian $varian)
    {
        return $this->collection(Gambar::where("detail_produk_id", $varian->detail_produk->id)->get(), \App::make(GambarTransformer::class), 'include');
    }
}