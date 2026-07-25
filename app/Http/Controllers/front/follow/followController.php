<?php

namespace App\Http\Controllers\front\follow;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\user\followsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class followController extends Controller
{
    public function toggle($username)
    {
        $user = User::where('username', $username)->first();
        $currentUser = User::where('id', Auth::id())->first();

        try {
            $existingFollow = followsModel::where('follower_id',$currentUser->id)
                ->where('followed_id',$user->id)
                ->first();

            if ($existingFollow) {
                $existingFollow->delete();
                User::where('id', $currentUser->id)->decrement('following');
                User::where('id', $user->id)->decrement('followers');
                $isFollowed = false;
                $message = 'unFollowed successfully';
            } else {
                $is_public = $user->is_public;
                $status = 'accepted';
                if ($is_public == 'false') {
                    $status = 'pending';
                }
                followsModel::create([
                    'follower_id' => $currentUser->id,
                    'followed_id' => $user->id,
                    'status' => $status
                ]);
                User::where('id', $currentUser->id)->increment('following');
                User::where('id', $user->id)->increment('followers');
                $isFollowed = true;
                $message = $is_public ? 'Followed successfully' : 'Follow request sent';
            }

            $user->refresh();
            $currentUser->refresh();

            return response()->json([
                'success' => true,
                'message' => $message,
                'isFollowed' => $isFollowed,
                'followersCount' => $user->followers,
                'formatted_count' => $this->formatCount($user->followers),
                'animation' => $isFollowed ? 'followed' : 'unFollowed'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' =>'an error occurred.',
            ], 500);
        }
    }
    private function formatCount($count)
    {
        if ($count >= 1000000) {
            return round($count / 1000000, 1) . 'M';
        } elseif ($count >= 1000) {
            return round($count / 1000, 1) . 'k';
        }
        return $count;
    }
}
