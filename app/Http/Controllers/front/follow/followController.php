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
        $user = User::where('username', $username)->firstOrFail();
        $currentUser = Auth::user();

        try {

            $existingFollow = followsModel::where('follower_id', $currentUser->id)
                ->where('followed_id', $user->id)
                ->first();

            if ($existingFollow) {

                if ($existingFollow->status === 'accepted') {
                    User::where('id', $currentUser->id)->decrement('following');
                    User::where('id', $user->id)->decrement('followers');
                }

                $existingFollow->delete();

                $isFollowed = false;
                $message = 'Unfollowed successfully';

            } else {

                $status = $user->privacy === 'public'
                    ? 'accepted'
                    : 'pending';

                followsModel::create([
                    'follower_id' => $currentUser->id,
                    'followed_id' => $user->id,
                    'status' => $status,
                ]);

                if ($status === 'accepted') {
                    User::where('id', $currentUser->id)->increment('following');
                    User::where('id', $user->id)->increment('followers');

                    $message = 'Followed successfully';
                } else {
                    $message = 'Follow request sent';
                }

                $isFollowed = true;
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
                'message' => 'An error occurred.',
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
    public function accept($id)
    {
        $request = followsModel::where('follower_id', $id)->where('followed_id', Auth::id())->update(['status' => 'accepted']);
        if ($request) {
            User::where('id', Auth::id())->increment('followers');
            User::where('id',$id)->increment('following');
            return back()->with('success', 'Accepted successfully');
        }
        return back()->with('fail', 'Something went wrong');
    }
}
