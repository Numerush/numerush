<?php
namespace App\Transformers;

use App\Models\Kategori;
use League\Fractal\TransformerAbstract;

class KategoriTransformer extends TransformerAbstract
{
    public function transform(Kategori $kat)
    {
        return [
            'id' => $kat->id,
            'nama_kategori' => $kat->nama_kategori
        ];
    }
}