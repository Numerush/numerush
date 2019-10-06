<?php
namespace App\Transformers;

use App\Models\Promo;
use League\Fractal\TransformerAbstract;

class PromoTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'kota'
    ];

    public function transform(Promo $promo)
    {
        return [
            'nama' => $promo->nama,
            'banner_path' => $promo->banner_path,
            'deskripsi' => $promo->deskripsi
        ];
    }
}