<?php

namespace App\Http\Controllers\admin\adminDashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\adminServices\dashboardUsersServices\dashboardUsersServices;
use Illuminate\Http\Request;
use Symfony\Component\Console\Tests\Fixtures\UserStatus;

class usersDashboardController extends Controller
{
    public function __construct(protected dashboardUsersServices $dashboardUsersServices)
    {

    }
    public function changeStatus($userId , $status)
    {
        $data = [
            'id' => $userId,
            'status' => $status
        ];
        return $this->dashboardUsersServices->ChangeUserStatus($data);
    }
}
