<?php

namespace App\Http\Controllers\admin\adminDashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\Console\Tests\Fixtures\UserStatus;

class usersDashboardController extends Controller
{
    public function userStatusToSuspended($userId)
    {
        $updatedUser = User::where('id', $userId)->update([
            'status' => 'suspend',
        ]);
        $suspendedCount =+ 1;
        if ($updatedUser) {
            return response([
                'status' => 'success',
                'message' => 'User is suspended successfully',
                'suspendedCount' => $suspendedCount,
            ]);
        }
        return response([
            'status' => 'error',
            'message' => 'something went wrong',
        ]);
    }
    public function userStatusToBanned($userId)
    {
        $updatedUser = User::where('id', $userId)->update([
            'status' => 'banned',
        ]);
        $bannedCount =+ 1;
        if ($updatedUser) {
            return response([
                'status' => 'success',
                'message' => 'User is banned successfully',
                'suspendedCount' => $bannedCount,
            ]);
        }
        return response([
            'status' => 'error',
            'message' => 'something went wrong',
        ]);
    }
    public function userStatusToActive($userId)
    {
        $updatedUser = User::where('id', $userId)->update([
            'status' => 'active',
        ]);
        $activeCount =+ 1;
        if ($updatedUser) {
            return response([
                'status' => 'success',
                'message' => 'User is active successfully',
                'suspendedCount' => $activeCount,
            ]);
        }
        return response([
            'status' => 'error',
            'message' => 'something went wrong',
        ]);
    }
}
