<?php

namespace App\Services\adminServices\adminDashboardServices;



use App\Models\posts\postModel;
use App\Models\reports\reportModel;
use App\Models\User;

class adminDashboardServices
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {

    }

    public function DashboardData()
    {
        $TotalUsers = User::all();
        $TotalUsersCount = $TotalUsers->count();
        $recentUsers = User::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->whereIn('role', ['user' , 'verifiedUser'])->take(5)->get();
        $suspendedUserCount = $TotalUsers->where('status', 'suspend')->count();
        $todayPostsCount = postModel::whereDate('created_at',today())->count();
        $postsAvg = $this->TodayPostsVsYesterday();
        $openReportsCount = reportModel::where('status', 'pending')->count();
        $thisMonthSuspendedUsers = $this->SuspendedUserCount();
        $contentBreakDown = $this->ContentBreakDown();
        $newOpenReports = $this->NewOpenReportsCount();

        return [
            'TotalUsers' => $TotalUsersCount,
            'todayPosts' => $todayPostsCount,
            'recentUsers' => $recentUsers,
            'openReportsCount' => $openReportsCount,
            'suspendedUserCount' => $suspendedUserCount,
            'postsAvg' => $postsAvg,
            'newOpenReportsCount' => $newOpenReports,
            'thisMonthSuspendedUsers' => $thisMonthSuspendedUsers,
            'contentBreakDown' => $contentBreakDown,
        ];
    }
    private function TodayPostsVsYesterday()
    {
        $todayPosts = postModel::whereDate('created_at', today())->count();
        $yesterdayPosts = postModel::whereDate('created_at', today()->subDay())->count();
        if ($yesterdayPosts == 0 && $todayPosts > 0) {
            return 100;
        }

        if ($yesterdayPosts == 0) {
            return 0;
        }

        return round((($todayPosts - $yesterdayPosts) / $yesterdayPosts) * 100, 2);
    }
    private function NewOpenReportsCount()
    {
        return reportModel::where('status','pending')->whereDate('created_at', today())->count();
    }
    private function SuspendedUserCount()
    {
        return User::where('status', 'suspend')->whereBetween('updated_at', [now()->startOfMonth(), now()->endOfMonth()])->count();
    }
    private function ContentBreakDown()
    {
        $posts = postModel::all();
        $videos = 0;
        $photos = 0;
        $files = [];
        foreach ($posts as $post) {
            explode(',',$post->post_files);
            $files  = array_merge($files,explode(',',$post->post_files));
        }
       foreach ($files as $file) {
           $path = pathinfo($file, PATHINFO_EXTENSION);
           if (in_array($path, ['mp4', 'mov', 'avi', 'webm'])){
               $videos += 1;
           }
           else{
               $photos += 1;
           }
       }
        $total = $videos + $photos;
        $photoPercent = $total > 0 ? round(($photos / $total) * 100, 2) : 0;
        $videoPercent = $total > 0 ? round(($videos / $total) * 100, 2) : 0;
        return [
            'total' => $total,
            'videos' => $videoPercent,
            'photos' => $photoPercent,
        ];
    }
    public function ShowUsers()
    {
        $users = User::where('role','user')->orderby('created_at', 'desc')->paginate(10);
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
        return [
            'users' => $users,
            'totalUsers' => $totalUsers,
            'activeUser' => $activeUser,
            'suspendedUsers' => $suspendedUsers,
            'bannedUsers' => $bannedUsers,
        ];
    }
    public function ShowPosts()
    {
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
        return [
            'posts' => $posts,
            'postsCount' => $postsCount,
            'todayPosts' => $todayPosts,
            'flaggedPosts' => $flaggedPosts,
            'hiddenPosts' => $hiddenPosts,
        ];
    }
    public function ShowFlaggedPosts()
    {
        $posts = postModel::with('user')->where('status', 'flagged')->orderBy('created_at', 'desc')->get();
        $posts->transform(function ($post) {
            $post->likes_formatted = $this->numberFormat($post->post_likes);
            $post->comments_formatted = $this->numberFormat($post->post_comments);
            return $post;
        });
        return response()->json($posts);
    }
    public function ShowHiddenPosts()
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
}
