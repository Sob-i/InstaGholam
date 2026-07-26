<?php

namespace App\Services\notificationsServices;


use App\Models\notifications\notificationModel;


class notificationsServices
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {

    }
    public function SendNotification(array $data)
    {
        notificationModel::create([
            'user_id' => $data['user_id'],
            'post_id' => $data['post_id'],
            'target_user_id' => $data['target_user_id'],
            'type' => $data['type'],
            'message' => $data['message'],
        ]);
    }
}
