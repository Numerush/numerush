<?php
namespace App\Transformers;

use App\Models\DoTrip;
use League\Fractal\TransformerAbstract;

class DoTripTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'varian', 'trip', 'dikirimke', 'user'
    ];

    public function transform(DoTrip $data)
    {
        // return [
        //     'id' => $data->id,
        //     // 'user_id' => $data->user_id,
        //     'trip_id' => $data->trip_id,
        //     'status' => $data->status,
        //     'jumlah' => $data->jumlah,
        //     // 'dibeli_dari' => $data->dibeli_dari,
        //     // 'dikirim_ke' => $data->dikirim_ke,
        //     // 'harga' => $data->harga,
        // ];

        return $data->toArray();
    }

    public function includeUser(DoTrip $data)
    {
        return $this->item($data->user, \App::make(UserTransformer::class), 'include');
    }

    public function includeTrip(DoTrip $data)
    {
        return $this->item($data->trip, \App::make(TripTransformer::class), 'include');
    }

    public function includeVarian(DoTrip $data)
    {
        return $this->item($data->varian, \App::make(VarianTransformer::class), 'include');
    }

    public function includeDikirimKe(DoTrip $data)
    {
        return $this->item($data->dikirimke, \App::make(AlamatTransformer::class), 'include');
    }
}