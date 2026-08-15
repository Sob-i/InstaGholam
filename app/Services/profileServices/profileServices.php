<?php

namespace App\Services\profileServices;



use App\Models\closeFriend\closeFriendModel;
use App\Models\highlights\highlightsModel;
use App\Models\posts\postModel;
use App\Models\posts\postsSaveModel;
use App\Models\User;
use App\Models\user\followsModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class profileServices
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {

    }

    private function CanViewProfile(User $viewer, User $owner)
    {
        if ($viewer->id == $owner->id)
            return true;

        if ($owner->privacy == 'public')
            return true;

        return followsModel::where('follower_id', $viewer->id)
            ->where('followed_id', $owner->id)->where('status', 'accepted')
            ->exists();
    }
    public function ShowProfile($user)
    {
        $Current = Auth::user();
        $owner = $user;
            $posts = PostModel::where('user_id', $user->id)->where('status','active')->with('comments')->orderBy('created_at', 'desc')->get();
            $postsCount = $posts->count();
            $followersCount = $user->followers;
            $followingCount = $user->following;
            $isFollowed = false;
            $highlights = highlightsModel::where('user_id' , $user->id)->get()->unique('cover')->values();
            $followingIds = followsModel::where('follower_id', $user->id)
                ->pluck('followed_id')
                ->toArray();
            $m[] = closeFriendModel::wherein('friend_id', $followingIds)
                ->where('user_id', $user->id)
                ->exists();
            $m[] = Auth::id();

            if (in_array($user->id, $m)) {
                $isCloseFriend = true;
            } else {
                $isCloseFriend = false;
            }

            if ($user) {
                $isFollowed = followsModel::where('follower_id', Auth::id())
                    ->where('followed_id', $user->id)->where('status' , 'accepted')
                    ->exists();
            }
            return [
                'posts' => $posts,
                'postsCount' => $postsCount,
                'followersCount' => $followersCount,
                'followingCount' => $followingCount,
                'isFollowed' => $isFollowed,
                'isCloseFriend' => $isCloseFriend,
                'canViewProfile' => $this->CanViewProfile($Current, $owner),
                'requested' => followsModel::where('follower_id', $Current->id)->where('status' , 'pending')->exists(),
                'highlights' => $highlights,
            ];

    }
    public function EditProfile($user , $data)
    {
        $fileName = strstr($user->email,'@',true);
        if (array_key_exists('avatar', $data)){
            $avatar = $this->avatarName($data['avatar'] , $fileName);
        }else{
            $avatar = $user->avatar;
        }

        $editedUser = User::where('id', Auth::id())->update([
            'avatar' => $avatar,
            'username' => $data['username'],
            'website' => $data['website'],
            'bio' => $data['bio'],
        ]);

        if($editedUser){
            return true;
        }else{
            return false;
        }
    }
    private function avatarName($avatarFile , $username)
    {
        $name = $username . 'avatar' . '.' . $avatarFile->getClientOriginalExtension();
        $this->moveAvatar($avatarFile,$name);
        return $name;
    }
    private function moveAvatar($avatarFile,$avatarName)
    {
        $avatarFile->move(public_path('users/avatar/') , $avatarName);
    }
    public function EditPassword($user , $data)
    {
        $currentPassword = $user['password'];
        global $newPassword;

        if(Hash::check($data['current_pass'], $currentPassword)){
            $newPassword = Hash::make($data['password']);
        }

        $editedUser = User::where('id', Auth::user()->id)->update([
            'password' => $newPassword,
        ]);

        if($editedUser){
            return true;
        }else{
            return false;
        }
    }
    public function CloseFriendToggle($user)
    {
        try {
            $id = request('friend_id');
            $existingCloseFriend = closeFriendModel::where('user_id',$user->id)
                ->where('friend_id' , $id)
                ->first();
            if ($existingCloseFriend){
                $existingCloseFriend->delete();
                $isCloseFriend = false;
                $message = "Removed from close friend list";
            }else{
                closeFriendModel::create([
                    'user_id' => $user->id,
                    'friend_id' => $id,
                ]);
                $isCloseFriend = true;
                $message = "Added to close friend list";
            }
            return response()->json([
                'success' => true,
                'message' => $message,
                'isCloseFriend' => $isCloseFriend,
                'animation' => $isCloseFriend ? 'friend' : 'unFriend'
            ]);
        }catch (\Exception $e)
        {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again.'
            ], 500);
        }
    }
    public function ShowFollowers($userId)
    {
        return followsModel::where('followed_id', $userId)->with('followerInfo')->get();
    }
    public function ShowFollowings($userId)
    {
        return followsModel::where('follower_id', $userId)->with('followingInfo')->get();
    }
    public function CreateHighlightCoverAndMove($cover,$title,$userEmail)
    {
        $name = 'highlightCover-' . $title . '-' . time() . '.' . $cover->getClientOriginalExtension();
        $cover->move(public_path("users/highlights/$userEmail/") , $name);
        return $name;
    }
    public function CreateHighlight(array $data)
    {
        $createdHighlights = [];

        foreach ($data['stories'] as $storyId) {

            $createdHighlights[] = highlightsModel::create([
                'title' => $data['title'],
                'cover' => $data['cover'],
                'user_id' => Auth::id(),
                'story_id' => $storyId,
            ]);

        }

        return $createdHighlights;
    }
    public function GetUserSavedPosts($userId)
    {
        $savedPosts = postsSaveModel::where('user_id', $userId)->with('post')->latest()->paginate(15);
        return response()->json([
            'status' => true,
            'saved_posts' => $savedPosts->items(),
            'current_page' => $savedPosts->currentPage(),
            'last_page' => $savedPosts->lastPage(),
            'has_more' => $savedPosts->hasMorePages(),
            'total' => $savedPosts->total(),
        ]);
    }

}
