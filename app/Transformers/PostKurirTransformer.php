<?php
namespace App\Transformers;

use App\Models\PostKurir;
use League\Fractal\TransformerAbstract;

class PostKurirTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'post', 'kurir'
    ];

    public function transform(PostKurir $kurir)
    {
        return [
            'kurir_id' => $kurir->kurir_id,
            'postdata_id' => $kurir->postdata_id,
            'postdata_type' => $kurir->postdata_type
        ];
    }

    public function includeKurir(PostKurir $data)
    {
        return $this->item($data->kurir, \App::make(KurirTransformer::class), 'include');
    }

    public function includePost(PostKurir $data)
    {
        if($data->tipe == 0)
            return $this->item($data->postdata, \App::make(PreorderTransformer::class),'include');
        else if($data->tipe == 1)
            return $this->item($data->postdata, \App::make(DoRequestingTransformer::class),'include');
        else if($data->tipe == 2)
            return $this->item($data->postdata, \App::make(TripTransformer::class),'include');
    }
}