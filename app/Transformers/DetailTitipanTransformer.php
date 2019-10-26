<?php
namespace App\Transformers;

use App\Models\DetailTitipan;
use League\Fractal\TransformerAbstract;

class DetailTitipanTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'varian', 'post', 'dikirimke', 'user', 'shopper','detailProduct'
    ];

    public function transform(DetailTitipan $data)
    {
        return $data->toArray();
    }

    public function includeUser(DetailTitipan $data)
    {
        return $this->item($data->user, \App::make(UserTransformer::class), 'include');
    }

    public function includeShopper(DetailTitipan $data)
    {
        return $this->item($data->shopper, \App::make(UserTransformer::class), 'include');
    }

    public function includeVarian(DetailTitipan $data)
    {
        return $this->item($data->varian, \App::make(VarianTransformer::class), 'include');
    }
    
    public function includePost(DetailTitipan $data)
    {
        if($data->tipe == 0)
            return $this->item($data->postdata, \App::make(PreorderTransformer::class),'include');
        else if($data->tipe == 1)
            return $this->item($data->postdata, \App::make(RequestingTransformer::class),'include');
        else if($data->tipe == 2)
            return $this->item($data->postdata, \App::make(TripTransformer::class),'include');
    }

    public function includeDikirimKe(DetailTitipan $data)
    {
        return $this->item($data->dikirimke, \App::make(AlamatTransformer::class), 'include');
    }

    public function includeDetailProduct(DetailTitipan $data)
    {
        return $this->item($data->varian, \App::make(VarianTransformer::class), 'include');
    }
}