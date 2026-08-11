<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\posts\postModel;
use App\Models\reports\reportModel;
use App\Models\User;
use App\Services\adminServices\adminDashboardServices\adminDashboardServices;
use App\Services\reportServices\reportServices;
use Illuminate\Http\Request;

class adminDashboardController extends Controller
{
    public function __construct(protected reportServices $reportServices , protected adminDashboardServices $adminDashboardServices)
    {

    }
    public function showDashboard()
    {
        $user = auth()->user();
        $data = $this->adminDashboardServices->DashboardData();
        return view('admin.dashboard.dashboard', compact('user', 'data'));
    }
    public function showUsers()
    {
        $data = $this->adminDashboardServices->ShowUsers();
        return view('admin.users.users', compact('data'));
    }
    public function showPosts()
    {
        $data = $this->adminDashboardServices->ShowPosts();
        return view('admin.posts.posts', compact('data'));
    }
    public function showPostsFlagged()
    {
        return $this->adminDashboardServices->ShowFlaggedPosts();
    }
    public function showPostsHidden()
    {
        return $this->adminDashboardServices->ShowHiddenPosts();
    }
    public function showReports()
    {
        $data = $this->reportServices->ReportsData();
        return view('admin.reports.reports', compact('data'));
    }
}
