<?php

namespace App\Services\exploreServices;


use App\Models\posts\postModel;
use App\Models\posts\postsSaveModel;
use App\Models\User;
use App\Models\user\followsModel;

class exploreServices
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {

    }
    public function Explore($request,$user)
    {
        $search = $request->input('search');
        $savedPosts = postsSaveModel::with('post')->orderBy('created_at', 'desc')->get();
        if ($search) {
            $posts =  postModel::where('post_audience', 'everyone')
                ->where(function($query) use ($search) {
                    $query->where('post_tags', 'like', '%' . $search . '%')
                        ->orWhere('post_location', 'like', '%' . $search . '%');
                })
                ->with('user', 'comments')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            $postsCount = $posts->count();
            $isFollowed [] = false;
            if ($user) {
                foreach ($posts as $post) {
                    $isFollowed = followsModel::where('follower_id', $user->id)
                        ->where('followed_id', $post->user_id)
                        ->exists();
                    $isFollowed = true;
                }
                return [
                    'posts' => $posts,
                    'postsCount' => $postsCount,
                    'isFollowed' => $isFollowed,
                    'savedPosts' => $savedPosts,
                ];
            }
        } else {
            $posts = PostModel::where('post_audience','everyone')->orderBy('created_at', 'desc')->get();
            $postsCount = $posts->count();
            $isFollowed [] = false;
            if ($user) {
                foreach ($posts as $post) {
                    $isFollowed = followsModel::where('follower_id', $user->id)
                        ->where('followed_id', $post->user_id)
                        ->exists();
                    $isFollowed = true;
                }
            }
            return [
                'posts' => $posts,
                'postsCount' => $postsCount,
                'isFollowed' => $isFollowed,
                'savedPosts' => $savedPosts,
            ];
        }
    }
    public function ExploreSearch($request)
    {
        $search = $request->input('search');
        $placesAndTags = $posts =  postModel::where('post_audience', 'everyone')
            ->where(function($query) use ($search) {
                $query->where('post_tags', 'like', '%' . $search . '%')
                    ->orWhere('post_location', 'like', '%' . $search . '%');
            })
            ->with('user','comments')
            ->get();
        $users = User::where('username', 'like', '%' . $search . '%')->get();

        if (!$placesAndTags && !$users) {
            return response()->json([
                'success' => false,
                'message' => 'nothing found',
            ]);
        }

        return response()->json([
            'success' => true,
            'placesAndTags' => $placesAndTags,
            'users' => $users,
        ]);
    }
}
