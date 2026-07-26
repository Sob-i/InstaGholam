<?php

namespace App\Observers;

use App\Models\posts\postsLikeModel;
use App\Services\notificationsServices\notificationsServices;
use Illuminate\Support\Facades\Auth;

class likeObserver
{
    public function __construct(protected notificationsServices $notificationsServices)
    {

    }
    /**
     * Handle the postsLikeModel "created" event.
     */
    public function created(postsLikeModel $postsLikeModel): void
    {
        $post = $postsLikeModel->post;
        $data = [
            'user_id' => Auth::user()->id,
            'post_id' => $post->id,
            'target_user_id' => $post->user_id,
            'type' => 'like',
            'message' => 'liked your post.'
        ];
        $this->notificationsServices->SendNotification($data);
    }

    /**
     * Handle the postsLikeModel "updated" event.
     */
    public function updated(postsLikeModel $postsLikeModel): void
    {
        //
    }

    /**
     * Handle the postsLikeModel "deleted" event.
     */
    public function deleted(postsLikeModel $postsLikeModel): void
    {
        //
    }

    /**
     * Handle the postsLikeModel "restored" event.
     */
    public function restored(postsLikeModel $postsLikeModel): void
    {
        //
    }

    /**
     * Handle the postsLikeModel "force deleted" event.
     */
    public function forceDeleted(postsLikeModel $postsLikeModel): void
    {
        //
    }
}
