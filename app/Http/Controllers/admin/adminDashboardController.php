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
        $users = User::orderby('created_at', 'desc')->paginate(10);
        $totalUsers = $users->count();
        $activeUser = $users->filter(function ($user) {
            return $user->status == 'active';
        })->count();
        $suspendedUsers = $users->filter(function ($user) {
            return $user->status == 'suspend';
        })->count();
        $bannedUsers = $users->filter(function ($user) {
            return $user->status == 'banned';
        })->count();
        return view('admin.users.users', compact('users', 'totalUsers', 'activeUser', 'suspendedUsers', 'bannedUsers'));
    }
    public function showPosts(){
        $posts = postModel::take(24)->latest()->with('user')->get();
        $postsCount = $this->numberFormat($posts->count());
        $todayPosts = $this->numberFormat($posts->filter(function ($post) {
            return $post->created_at->isToday();
        })->count());
        $flaggedPosts = $this->numberFormat($posts->filter(function ($post) {
            return $post->status == 'flagged';
        })->count());
        $hiddenPosts = $this->numberFormat($posts->filter(function ($post) {
            return $post->status == 'hidden';
        })->count());
        $posts->transform(function ($post) {
            $post->likes_formatted = $this->numberFormat($post->post_likes);
            $post->comments_formatted = $this->numberFormat($post->post_comments);
            return $post;
        });

        return view('admin.posts.posts', compact('posts', 'postsCount', 'todayPosts','flaggedPosts','hiddenPosts'));
    }
    public function showPostsFlagged()
    {
        $posts = postModel::with('user')->where('status', 'flagged')->orderBy('created_at', 'desc')->get();
        $posts->transform(function ($post) {
            $post->likes_formatted = $this->numberFormat($post->post_likes);
            $post->comments_formatted = $this->numberFormat($post->post_comments);
            return $post;
        });
        return response()->json($posts);
    }
    public function showPostsHidden()
    {
        $posts = postModel::with('user')->where('status', 'hidden')->orderBy('created_at', 'desc')->get();
        $posts->transform(function ($post) {
            $post->likes_formatted = $this->numberFormat($post->post_likes);
            $post->comments_formatted = $this->numberFormat($post->post_comments);
            return $post;
        });
        return response()->json($posts);
    }
    private function numberFormat($number)
    {
        if ($number >= 1000000) {
            return round($number / 1000000, 1) . 'M';
        }
        if ($number >= 1000) {
            return round($number / 1000, 1) . 'k';
        }
        return $number;
    }
    public function showReports()
    {
        $data = $this->reportServices->ReportsData();
        return view('admin.reports.reports', compact('data'));
    }
}
