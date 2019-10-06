<?php
namespace App\Transformers;

use App\Models\Alamat;
use App\Models\DefaultAlamat;
use League\Fractal\TransformerAbstract;

class AlamatTransformer extends TransformerAbstract
{

    public function transform(Alamat $alamat)
    {
        $def = DefaultAlamat::where('user_id',$alamat->user_id)->first();

        if($def->alamat_id == $alamat->id)
            $is_def = 1;
        else {
            $is_def = 0;
        }
        return [
            'id' => $alamat->id,
            'user_id' => $alamat->user_id,
            'jalan' => $alamat->jalan,
            'kode_pos' => $alamat->kode_pos,
            'kota_rajaongkir' => $alamat->kota_rajaongkir,
            'is_default' => $is_def
        ];
    }

}