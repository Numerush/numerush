<?php
namespace App\Transformers;

use App\Models\DefaultAlamat;
use League\Fractal\TransformerAbstract;

class DefaultAlamatTransformer extends TransformerAbstract
{

    protected $availableIncludes = [
        'alamat'
    ];

    public function transform(DefaultAlamat $def)
    {
        return [
            'user_id' => $def->user_id,
            'alamat_id' => $def->alamat_id,
        ];
    }

    public function includeAlamat(DefaultAlamat $data)
    {
        return $this->item($data->alamat, \App::make(AlamatTransformer::class),'include');
    }
}