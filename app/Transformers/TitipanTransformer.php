<?php
namespace App\Transformers;

use App\Models\Titipan;
use League\Fractal\TransformerAbstract;

class TitipanTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'varian', 'post', 'dikirimke', 'user', 'shopper'
    ];

    public function transform(Titipan $data)
    {
        return $data->toArray();
    }

    public function includeUser(Titipan $data)
    {
        return $this->item($data->user, \App::make(UserTransformer::class), 'include');
    }

    public function includeShopper(Titipan $data)
    {
        return $this->item($data->shopper, \App::make(UserTransformer::class), 'include');
    }

    public function includeVarian(Titipan $data)
    {
        return $this->item($data->varian, \App::make(VarianTransformer::class), 'include');
    }
    
    public function includePost(Titipan $data)
    {
        if($data->tipe == 0)
            return $this->item($data->postdata, \App::make(PreorderTransformer::class),'include');
        else if($data->tipe == 1)
            return $this->item($data->postdata, \App::make(RequestingTransformer::class),'include');
        else if($data->tipe == 2)
            return $this->item($data->postdata, \App::make(TripTransformer::class),'include');
    }

    public function includeDikirimKe(Titipan $data)
    {
        return $this->item($data->dikirimke, \App::make(AlamatTransformer::class), 'include');
    }
}