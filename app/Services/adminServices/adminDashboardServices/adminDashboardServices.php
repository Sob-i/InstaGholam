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
        $todayReports = reportModel::where('status','pending')->whereDate('created_at', today())->count();
        $yesterdayReports = reportModel::where('status','pending')->whereDate('created_at', today()->subDay())->count();
        if ($yesterdayReports == 0 && $todayReports > 0) {
            return 1;
        }

        if ($yesterdayReports == 0) {
            return 0;
        }
        return $yesterdayReports > 0 ? $yesterdayReports - $todayReports : 0;
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
}
