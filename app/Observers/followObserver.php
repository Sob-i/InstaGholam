<?php

namespace App\Observers;

use App\Models\user\followsModel;
use App\Services\notificationsServices\notificationsServices;
use Illuminate\Support\Facades\Auth;

class followObserver
{
    public function __construct(protected notificationsServices $notificationsServices)
    {

    }
    /**
     * Handle the followsModel "created" event.
     */
    public function created(followsModel $followsModel): void
    {

        $data = [
            'user_id' => Auth::user()->id,
            'target_user_id' => $followsModel->followed_id,
            'type' => 'follow',
            'message' => ' started following you.'
        ];
        $this->notificationsServices->SendNotification($data);
    }

    /**
     * Handle the followsModel "updated" event.
     */
    public function updated(followsModel $followsModel): void
    {
        //
    }

    /**
     * Handle the followsModel "deleted" event.
     */
    public function deleted(followsModel $followsModel): void
    {
        //
    }

    /**
     * Handle the followsModel "restored" event.
     */
    public function restored(followsModel $followsModel): void
    {
        //
    }

    /**
     * Handle the followsModel "force deleted" event.
     */
    public function forceDeleted(followsModel $followsModel): void
    {
        //
    }
}
