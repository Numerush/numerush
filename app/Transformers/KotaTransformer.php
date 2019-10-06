<?php
namespace App\Transformers;

use App\Models\Kota;
use League\Fractal\TransformerAbstract;

class KotaTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'negara'
    ];

    public function transform(Kota $kota)
    {
        return [
            'id' => $kota->id,
            'nama_kota' => $kota->nama_kota
        ];
    }

    public function includeNegara(Kota $kota)
    {
        return $this->item($kota->negara, \App::make(NegaraTransformer::class),'include');
    }
}