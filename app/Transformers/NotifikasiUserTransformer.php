<?php
namespace App\Transformers;

use App\Models\NotifikasiUser;
use League\Fractal\TransformerAbstract;

class NotifikasiUserTransformer extends TransformerAbstract
{
    public function transform(NotifikasiUser $notif)
    {
        return [
            'id' => $notif->id,
            // 'user_id' => $notif->user_id,
            'pesan' => $notif->pesan,
            'already_read' => $notif->already_read,
            'created_at' => $notif->created_at->getTimestamp(),
        ];
    }
}