<?php

namespace App\Services\adminServices\dashboardUsersServices;




use App\Models\posts\postModel;
use App\Models\User;

class dashboardUsersServices
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {

    }
    public function ChangeUserStatus(array $data)
    {
        $updatedUser = User::where('id',$data['id'])->update([ 'status' => $data['status'] ]);
        $status = $data['status'];
        if ($updatedUser) {
            $Count =+ 1;
            return response([
                'status' => 'success',
                'message' => "$status User successfully",
                $status.'Count' => $Count,
            ]);
        }
        return response([
            'status' => 'error',
            'message' => 'something went wrong',
        ]);
    }

}

