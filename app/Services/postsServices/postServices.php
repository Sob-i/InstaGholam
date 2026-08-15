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

        $postFiles = explode(',', $post->post_files);
        $date = $post->created_at->format('Y-m-d');
        $folderName = strstr($post->user->email, '@', true);

        return [
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
    public function GetComments($postId)
    {
        $comments = commentModel::where('post_id', $postId)
            ->where('type', 'comment')
            ->orderBy('created_at', 'desc')
            ->with([
                'user:id,username,avatar,role',
                'post:id,user_id'
            ])
            ->withCount([
                'replies as replies_count'
            ])
            ->paginate(15);

        $commentsData = $comments->getCollection()->map(function ($comment) {

            $data = $comment->toArray();

            $data['created_at'] = $comment->created_at->diffForHumans();

            $data['can_report'] = auth()->id() != $comment->user_id;

            $data['can_delete'] =
                auth()->id() == $comment->user_id ||
                auth()->id() == $comment->post->user_id;

            return $data;
        });

        return response()->json([
            'status' => true,
            'comments' => $commentsData,
            'current_page' => $comments->currentPage(),
            'last_page' => $comments->lastPage(),
            'has_more' => $comments->hasMorePages(),
            'total' => $comments->total(),
        ]);
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
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'created_at' => $comment->created_at->diffForHumans(),
                    'user_id' => $comment->user_id,
                    'user' => [
                        'username' => $comment->user->username,
                        'avatar' => $comment->user->avatar,
                        'role' => $comment->user->role,
                    ],
                    'can_report' => auth()->id() != $comment->user_id,
                    'can_delete' =>
                        auth()->id() == $comment->user_id ||
                        auth()->id() == $comment->post->user_id,
                    'replies_count' => 0,
                ]
            ]);
        }
        return response()->json([
            'success' => false,
        ]);
    }
    public function GetCommentReplies($postId, $commentId)
    {
        $replies = commentModel::where('post_id', $postId)
            ->where('reply_comment_id', $commentId)
            ->where('type', 'reply')
            ->orderBy('created_at', 'asc')
            ->with([
                'user:id,username,avatar,role',
                'post:id,user_id',
            ])
            ->withCount([
                'replies as replies_count'
            ])
            ->paginate(15);

        $repliesData = $replies->getCollection()->map(function ($reply) {

            $data = $reply->toArray();

            $data['created_at'] = $reply->created_at->diffForHumans();

            $data['can_report'] =
                auth()->id() != $reply->user_id;

            $data['can_delete'] =
                auth()->id() == $reply->user_id ||
                auth()->id() == $reply->post->user_id;

            return $data;
        });

        return response()->json([
            'status' => true,
            'replies' => $repliesData,
            'current_page' => $replies->currentPage(),
            'last_page' => $replies->lastPage(),
            'has_more' => $replies->hasMorePages(),
            'total' => $replies->total(),
        ]);
    }
    public function AddCommentReply($data)
    {
        $commentReply = commentModel::create([
            'post_id' => $data['post_id'],
            'user_id' => $data['user_id'],
            'reply_comment_id' => $data['comment_id'],
            'content' => $data['reply']['reply'],
            'type' => $data['type'],
        ]);

        $commentReply->load('user:id,username,avatar,role');

        $commentReply->loadCount([
            'replies as replies_count'
        ]);

        if ($commentReply) {

            postModel::where('id', $data['post_id'])
                ->increment('post_comments');

            return response()->json([
                'success' => true,
                'reply' => [
                    'id' => $commentReply->id,
                    'reply_comment_id' => $commentReply->reply_comment_id,
                    'content' => $commentReply->content,
                    'created_at' => $commentReply->created_at->diffForHumans(),

                    'replies_count' => $commentReply->replies_count,

                    'can_report' => auth()->id() != $commentReply->user_id,

                    'can_delete' =>
                        auth()->id() == $commentReply->user_id ||
                        auth()->id() == $commentReply->post->user_id,

                    'user' => [
                        'id' => $commentReply->user->id,
                        'username' => $commentReply->user->username,
                        'avatar' => $commentReply->user->avatar,
                        'role' => $commentReply->user->role,
                    ],
                ]
            ]);
        }
        return response()->json([
            'success' => false,
        ]);
    }
    public function DeleteComment($data)
    {
        $comment = commentModel::where('post_id', $data['post_id'])
            ->where('id', $data['comment_id'])
            ->first();

        $post = postModel::find($data['post_id']);

        if (!$comment || !$post) {
            return response()->json([
                'success' => false,
                'message' => 'Comment or post not found.',
            ], 404);
        }

        if (
            auth()->id() != $comment->user_id &&
            auth()->id() != $post->user_id
        ) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete this comment.',
            ], 403);
        }

        $deletedReplies = $this->DeleteCommentReply($comment->id);

        $deletedComment = $comment->delete();

        if ($deletedComment) {

            $totalDeleted = 1 + $deletedReplies;

            $post->decrement(
                'post_comments',
                $totalDeleted
            );

            return response()->json([
                'success' => true,
                'commentCount' => -$totalDeleted,
                'replies_count' => $deletedReplies,
            ]);
        }

        return response()->json([
            'success' => false,
        ]);
    }
    public function DeleteCommentReply($parentId)
    {
        $replies = commentModel::where('type', 'reply')
            ->where('reply_comment_id', $parentId)
            ->get();

        $deletedCount = 0;

        foreach ($replies as $reply) {

            $deletedCount += $this->DeleteCommentReply($reply->id);

            $reply->delete();

            $deletedCount++;
        }

        return $deletedCount;
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
