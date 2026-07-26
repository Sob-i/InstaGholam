<?php

namespace App\Observers;

use App\Models\comments\commentModel;
use App\Services\notificationsServices\notificationsServices;
use Illuminate\Support\Facades\Auth;

class commentObserver
{
    public function __construct(protected notificationsServices $notificationsServices)
    {

    }
    /**
     * Handle the commentModel "created" event.
     */
    public function created(commentModel $commentModel): void
    {
        $post = $commentModel->post;
        $data = [
            'user_id' => Auth::user()->id,
            'post_id' => $post->id,
            'target_user_id' => $post->user_id,
            'type' => 'comment',
            'message' => " commented : '$commentModel->content' "
        ];
        $this->notificationsServices->SendNotification($data);
    }

    /**
     * Handle the commentModel "updated" event.
     */
    public function updated(commentModel $commentModel): void
    {
        //
    }

    /**
     * Handle the commentModel "deleted" event.
     */
    public function deleted(commentModel $commentModel): void
    {
        //
    }

    /**
     * Handle the commentModel "restored" event.
     */
    public function restored(commentModel $commentModel): void
    {
        //
    }

    /**
     * Handle the commentModel "force deleted" event.
     */
    public function forceDeleted(commentModel $commentModel): void
    {
        //
    }
}
