<?php
namespace App\Transformers;

use App\Models\DoPreorder;
use League\Fractal\TransformerAbstract;

class DoPreorderTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'preorder', 'varian', 'dikirimke' ,'user'
    ];

    public function transform(DoPreorder $data)
    {
        // return [
        //     'id' => $data->id,
        //     // 'user_id' => $data->user_id,
        //     'preorder_id' => $data->preorder_id,
        //     // 'dikirim_ke' => $data->dikirim_ke,
        //     'status' => $data->status,
        //     'jumlah' => $data->jumlah,
        //     // 'harga' => $data->harga,
        // ];

        return $data->toArray();
    }

    public function includeUser(DoPreorder $data)
    {
        return $this->item($data->user, \App::make(UserTransformer::class), 'include');
    }

    public function includePreorder(DoPreorder $data)
    {
        return $this->item($data->preorder, \App::make(PreorderTransformer::class), 'include');
    }

    public function includeVarian(DoPreorder $data)
    {
        return $this->item($data->varian, \App::make(VarianTransformer::class), 'include');
    }

    public function includeDikirimKe(DoPreorder $data)
    {
        return $this->item($data->dikirimke, \App::make(AlamatTransformer::class), 'include');
    }
}