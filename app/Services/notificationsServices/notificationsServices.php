<?php

namespace App\Services\notificationsServices;


use App\Models\notifications\notificationModel;
use App\Models\user\followsModel;


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
        switch ($data['type']) {

            case 'follow':
                notificationModel::where([
                    'user_id' => $data['user_id'],
                    'target_user_id' => $data['target_user_id'],
                    'type' => 'follow',
                ])->delete();
                break;

            case 'like':
                notificationModel::where([
                    'user_id' => $data['user_id'],
                    'target_user_id' => $data['target_user_id'],
                    'post_id' => $data['post_id'],
                    'type' => 'like',
                ])->delete();
                break;

            case 'request':
                notificationModel::where([
                    'user_id' => $data['user_id'],
                    'target_user_id' => $data['target_user_id'],
                    'type' => 'request',
                ])->delete();
                break;
        }

        notificationModel::create([
            'user_id' => $data['user_id'],
            'post_id' => $data['post_id'] ?? null,
            'target_user_id' => $data['target_user_id'],
            'type' => $data['type'],
            'message' => $data['message'],
        ]);
    }
    public function NotificationData($user)
    {
        $notifications = notificationModel::where('target_user_id' , $user->id)->with(['user:id,username,avatar,email', 'targetUser' ,'post'])->orderBy('created_at', 'desc')->get();
        $newNotifications = collect();
        $oldNotifications = collect();

        foreach ($notifications as $notification) {

            if ($notification->type === 'follow') {
                $notification->isFollowed = followsModel::where('follower_id', $user->id)
                    ->where('followed_id', $notification->user_id)
                    ->exists();
            } else {
                $notification->isFollowed = followsModel::where('follower_id', $notification->target_user_id)
                    ->exists();
            }

            if ($notification->created_at->isToday()) {
                $newNotifications->push($notification);
            } else {
                $oldNotifications->push($notification);
            }
        }

        return [
            'newNotifications' => $newNotifications,
            'oldNotifications' => $oldNotifications,
        ];
    }
}
