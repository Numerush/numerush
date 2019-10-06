<?php
namespace App\Transformers;

use App\Models\Kurir;
use League\Fractal\TransformerAbstract;

class KurirTransformer extends TransformerAbstract
{

    public function transform(Kurir $kurir)
    {
        return [
            'id' => $kurir->id,
            'kode' => $kurir->kode,
            'nama_jasa' => $kurir->nama_jasa
        ];
    }
}