<?php
namespace App\Transformers;

use App\Models\DoRequesting;
use League\Fractal\TransformerAbstract;

class DoRequestingTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'requesting', 'varian', 'dibelidari', 'user'
    ];

    public function transform(DoRequesting $data)
    {
        // return [
        //     'id' => $data->id,
        //     // 'user_id' => $data->user_id,
        //     'requesting_id' => $data->requesting_id,
        //     'status' => $data->status,
        //     // 'dibeli_dari' => $data->dibeli_dari,
        //     'dikirim_dari' => $data->dikirim_dari,
        //     // 'harga' => $data->harga,
        // ];

        return $data->toArray();
    }

    public function includeUser(DoRequesting $data)
    {
        return $this->item($data->user, \App::make(UserTransformer::class), 'include');
    }

    public function includeRequesting(DoRequesting $data)
    {
        return $this->item($data->requesting, \App::make(RequestingTransformer::class), 'include');
    }

    public function includeVarian(DoRequesting $data)
    {
        return $this->item($data->varian, \App::make(VarianTransformer::class), 'include');
    }

    public function includeDibeliDari(DoRequesting $data)
    {
        return $this->item($data->dibelidari, \App::make(KotaTransformer::class), 'include');
    }
}