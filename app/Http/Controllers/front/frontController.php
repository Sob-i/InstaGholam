<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\notifications\notificationModel;
use App\Models\User;
use App\Services\postsServices\postServices;
use App\Services\profileServices\profileServices;
use Illuminate\Support\Facades\Auth;

class frontController extends Controller
{
    public function __construct(protected postServices $postServices , protected profileServices $profileServices)
    {

    }
    public function index(){
        $user = User::where('id', Auth::id())->with('closeFriend')->first();
        $data = $this->postServices->ShowAllPosts($user);
        return view('index.homePage.homePage', compact('user', 'data'));
    }

    public function newPost()
    {
        $user = Auth::user();
        return view('index.newPost.newPost', compact('user'));
    }

    public function newStoryShow()
    {
        $user = Auth::user();
        return view('index.newStory.newStory', compact('user'));
    }

    public function notificationsShow()
    {
        $user = Auth::user();
        $notifications = notificationModel::where('target_user_id' , $user->id)->with(['user:id,username,avatar,email', 'post'])->orderBy('created_at', 'desc')->get();
        return view('index.notifications.notifications', compact('user', 'notifications'));
    }

    public function profile($username)
    {
        $user = User::where('username', $username)->first();
        $data = $this->profileServices->ShowProfile($user);
        return view('index.profile.profile', compact('user', 'data'));
    }

}
