<?php
namespace App\Transformers;

use App\Models\Titipan;
use App\Models\Requesting;
use App\Models\Trip;
use App\Models\Preorder;
use League\Fractal\TransformerAbstract;
use App\Models\DetailTitipan;
use App\User;

class TitipanTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'varian', 'post', 'dikirimke', 'user', 'shopper', 'detail'
    ];

    public function transform(Titipan $data)
    {
        return $data->toArray();
    }

    public function includeUser(Titipan $data)
    {
        return $this->item(User::find($data->user_id), \App::make(UserTransformer::class), 'include');
    }

    public function includeShopper(Titipan $data)
    {
        return $this->item(User::find($data->shopper_id), \App::make(UserTransformer::class), 'include');
    }

    public function includeVarian(Titipan $data)
    {
        return $this->item($data->varian, \App::make(VarianTransformer::class), 'include');
    }
    
    public function includePost(Titipan $data)
    {
        $detail=DetailTitipan::where("titipan_id", $data->id)->first();
        if($detail->postdata_type == "App\Models\Preorder")
            return $this->item(Preorder::find($detail->postdata_id), \App::make(PreorderTransformer::class),'include');
        else if($detail->postdata_type == "App\Models\Requesting")
            return $this->item(Requesting::find($detail->postdata_id), \App::make(RequestingTransformer::class),'include');
        else if($detail->postdata_type == "App\Models\Trip")
            return $this->item(Trip::find($detail->postdata_id), \App::make(TripTransformer::class),'include');
    }

    public function includeDikirimKe(Titipan $data)
    {
        return $this->item($data->dikirimke, \App::make(AlamatTransformer::class), 'include');
    }

    public function includeDetail(Titipan $data)
    {
        return $this->item(DetailTitipan::where("titipan_id", $data->id)->first(), \App::make(DetailTitipanTransformer::class), 'include');
    }
    
}