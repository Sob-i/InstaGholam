<?php

namespace App\Services\postsServices;


use App\Models\closeFriend\closeFriendModel;
use App\Models\comments\commentModel;
use App\Models\posts\postModel;
use App\Models\posts\postsLikeModel;
use App\Models\posts\postsSaveModel;
use App\Models\story\storyModel;
use App\Models\user\followsModel;
use App\Services\notificationsServices\notificationsServices;
use Illuminate\Support\Facades\Auth;

class postServices
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {

    }
    public function ShowSinglePost($user , $post)
    {
        $followingIds = followsModel::where('follower_id', $post->user_id)
            ->pluck('followed_id')
            ->toArray();

        $closeFriendRecord = closeFriendModel::where('user_id', $post->user_id)
            ->where('friend_id', $user->id)
            ->first();

        $isCloseFriend = false;
        if ($closeFriendRecord || $post->user_id == $user->id) {
            $isCloseFriend = true;
        }

        if ($post->post_audience == 'closeFriends' && !$isCloseFriend) {
            return redirect()->route('homepage');
        }

        $isFollowed = false;
        if (followsModel::where('follower_id', $user->id)->where('followed_id', $post->user_id)->exists()) {
            $isFollowed = true;
        }

        $comments = CommentModel::where('post_id', $post->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $postFiles = explode(',', $post->post_files);
        $date = $post->created_at->format('Y-m-d');
        $folderName = strstr($post->user->email, '@', true);

        return [
            'comments' => $comments ,
            'postFiles' => $postFiles ,
            'date' => $date ,
            'folderName' => $folderName ,
            'isFollowed' => $isFollowed,
            'isCloseFriend' => $isCloseFriend
        ];
    }
    public function ShowAllPosts($user)
    {
        $followingIds = followsModel::where('follower_id', $user->id)
            ->pluck('followed_id')
            ->toArray();
        $followingIds[] = $user->id;
        $isCloseFriendsId = closeFriendModel::where('friend_id', $user->id)
            ->whereIn('user_id', $followingIds)
            ->pluck('friend_id')
            ->toArray();

        $storiesForCircles = storyModel::whereIn('user_id', $followingIds)
            ->where('status', 'active')
            ->orderBy('created_at', 'asc')
            ->with('user')
            ->get()
            ->filter(function ($story) use ($user, $isCloseFriendsId) {

                if ($story->audience === 'followers') {
                    return true;
                }

                if ($story->user_id == $user->id) {
                    return true;
                }

                return in_array($user->id, $isCloseFriendsId);
            })
            ->unique('user_id')
            ->values();

        $allStories = storyModel::whereIn('user_id', $followingIds)
            ->where('status', 'active')
            ->orderBy('created_at', 'asc')
            ->with('user')
            ->get();

        $posts = postModel::whereIn('user_id', $followingIds)->where('status', 'active')
            ->with([
                'user',
                'comments' => function ($query) {
                    $query->with('user')->orderBy('created_at', 'desc');
                }
            ])
            ->orderByDesc('created_at')
            ->paginate(20);

        $posts->getCollection()->transform(function ($post) use ($user) {

            $post->media_files = explode(',', $post->post_files);
            $post->post_date = $post->created_at->format('Y-m-d');
            $post->post_folder_name = strstr($post->post_files, '-', true);

            $post->isFollowed = followsModel::where('follower_id', $user->id)
                ->where('followed_id', $post->user_id)
                ->exists();

            $post->isLikedByUser = postsLikeModel::where('user_id', $user->id)
                ->where('post_id', $post->id)
                ->exists();

            $post->isSavedByUser = postsSaveModel::where('user_id', $user->id)
                ->where('post_id', $post->id)
                ->exists();

            return $post;
        });

        $storiesJson = $allStories->map(function ($story) use ($user,$isCloseFriendsId) {
            if ($story->user_id == $user->id) {
                $isCloseFriendsId[] = $story->user_id;
            }
            return [
                'id' => $story->id,
                'user_id' => $story->user_id,
                'media' => $story->media,
                'media_type' => $story->media_type,
                'email_prefix' => strstr($story->user->email, '@', true),
                'status' => $story->status,
                'audience' => $story->audience,
                'is_close_friend' => in_array($user->id, $isCloseFriendsId),
                'created_at' => $story->created_at->diffForHumans(),
                'user' => [
                    'username' => $story->user->username,
                    'avatar' => $story->user->avatar,
                ],
            ];
        });

        return [
            'posts' => $posts,
            'allStories' => $allStories,
            'stories' => $storiesJson,
            'storiesForCircles' => $storiesForCircles,
            'isCloseFriendsId' => $isCloseFriendsId,
        ];
    }
    public function CreatePost($data)
    {
        try {
            $Filename = strstr($data['user']->email,'@',true);
            $f = $this->postName($data['file'], $Filename);

            postModel::create([
                'user_id' => $data['user']->id,
                'post_files'=> $f,
                'post_tags' => $data['postData']['tags'],
                'post_caption' => $data['postData']['caption'],
                'post_location' => $data['postData']['location'],
                'post_audience' => $data['postData']['audience'],
                'comment_status' => $data['postData']['disable_comments'],
                'like_count' => $data['postData']['hide_likes'],
            ]);
            return true;
        }catch (\Exception $e){
        }
        return false;
    }
    private function postName($files , $name){
        $uploadedFiles = [];
        foreach ($files as $file) {
            $fileName = $name.'-'.time().rand('11','99').'.'.$file->getClientOriginalExtension();
            $this->storePost($file, $name , $fileName);
            $uploadedFiles[] = $fileName;
        }
        return implode(',',$uploadedFiles);
    }
    private function storePost ($file , $folderName , $fileName){
        $timestamp = date('Y-m-d',time());
        $file->move(public_path("users/posts/$folderName-posts/$timestamp"), $fileName);
    }
    public function AddComment($data)
    {
        $comment = commentModel::create([
            'post_id' => $data['post_id'],
            'user_id' => $data['user_id'],
            'content' => $data['comment']['comment']
        ]);

        $comment->load('user');
        if($comment){
            postModel::where('id', $data['post_id'])->increment('post_comments');
            return response()->json([
                'success' => true,
                'comment' => [
                    'content' => $comment->content,
                    'created_at' => $comment->created_at->diffForHumans(),
                    'user' => [
                        'name' => $comment->user->username,
                        'avatar' => $comment->user->avatar,
                        ''
                    ]
                ]
            ]);
        }
        return response()->json([
            'success' => false,
        ]);
    }
    public function LikePost($user , $post)
    {
        try {
            $existingLike = postsLikeModel::where('user_id', $user->id)
                ->where('post_id', $post->id)
                ->first();

            if ($existingLike) {

                $existingLike->delete();
                $post->decrement('post_likes');
                $isLiked = false;
                $message = 'Post unliked successfully';

            } else {

                postsLikeModel::create([
                    'user_id' => $user->id,
                    'post_id' => $post->id
                ]);
                $post->increment('post_likes');
                $isLiked = true;
                $message = 'Post liked successfully';
            }

            if ($post->like_count == 'visible') {
                $likesCount = $post->likes()->count();
                $formattedCount = $this->formatCount($likesCount);
                $showCount = true;
            } else {
                $likesCount = null;
                $formattedCount = null;
                $showCount = false;
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'is_liked' => $isLiked,
                'likes_count' => $likesCount,
                'formatted_count' => $formattedCount,
                'show_count' => $showCount,
                'animation' => $isLiked ? 'like' : 'unlike'
            ]);



        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again.'
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
    public function SavePost($user , $post)
    {
        try {
            $existingSave = postsSaveModel::where('user_id',$user->id)->where('post_id',$post->id)->first();

            if ($existingSave) {
                $existingSave->delete();
                $isSaved = false;
                $message = 'Post unsaved successfully';
            }else{
                postsSaveModel::create([
                    'user_id' => $user->id,
                    'post_id' => $post->id
                ]);
                $isSaved = true;
                $message = 'Post saved successfully';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'is_saved' => $isSaved,
                'animation' => $isSaved ? 'saved' : 'unsaved'
            ]);


        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

}
