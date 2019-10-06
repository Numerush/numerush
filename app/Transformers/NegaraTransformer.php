<?php
namespace App\Transformers;

use App\Models\Negara;
use League\Fractal\TransformerAbstract;

class NegaraTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'kota'
    ];

    public function transform(Negara $negara)
    {
        return [
            'id' => $negara->id,
            'nama_negara' => $negara->nama_negara,
            'bendera_path' => url($negara->bendera_path),
            'wallpaper' => url($negara->wallpaper),
        ];
    }

    public function includeKota(Negara $negara)
    {
        return $this->collection($negara->kota, \App::make(KotaTransformer::class), 'include');
    }
}