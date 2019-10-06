<?php
namespace App\Transformers;

use App\Models\DefaultKurir;
use League\Fractal\TransformerAbstract;

class DefaultKurirTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'kurir'
    ];

    public function transform(DefaultKurir $kurir)
    {
        return [
            'kurir_id' => $kurir->kurir_id
        ];
    }

    public function includeKurir(DefaultKurir $data)
    {
        return $this->item($data->kurir, \App::make(KurirTransformer::class), 'include');
    }
}