<?php
namespace App\Transformers;

use App\Models\BoxTitipan;
use League\Fractal\TransformerAbstract;

class BoxTitipanTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'varian', 'post', 'dikirimke', 'user', 'shopper'
    ];

    public function transform(BoxTitipan $data)
    {
        return $data->toArray();
    }

    public function includeUser(BoxTitipan $data)
    {
        return $this->item($data->user, \App::make(UserTransformer::class), 'include');
    }

    public function includeShopper(BoxTitipan $data)
    {
        return $this->item($data->shopper, \App::make(UserTransformer::class), 'include');
    }

    public function includeVarian(BoxTitipan $data)
    {
        return $this->item($data->varian, \App::make(VarianTransformer::class), 'include');
    }
    
    public function includePost(BoxTitipan $data)
    {
        if($data->tipe == 0)
            return $this->item($data->postdata, \App::make(PreorderTransformer::class),'include');
        else if($data->tipe == 1)
            return $this->item($data->postdata, \App::make(RequestingTransformer::class),'include');
        else if($data->tipe == 2)
            return $this->item($data->postdata, \App::make(TripTransformer::class),'include');
    }

    public function includeDikirimKe(BoxTitipan $data)
    {
        return $this->item($data->dikirimke, \App::make(AlamatTransformer::class), 'include');
    }
}