<?php
namespace App\Transformers;

use App\Models\Gambar;
use League\Fractal\TransformerAbstract;

class GambarTransformer extends TransformerAbstract
{

    public function transform(Gambar $gambar)
    {
        return [
            'path_gambar' => url($gambar->path_gambar)
        ];
    }
}