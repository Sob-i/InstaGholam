<?php

namespace App\Http\Controllers\front\profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\profile\profileEditInfoReqeust;
use App\Http\Requests\profile\profileEditPasswordReqeust;
use App\Models\closeFriend\closeFriendModel;
use App\Models\comments\commentModel;
use App\Models\posts\postModel;
use App\Models\User;
use App\Models\user\followsModel;
use App\Services\profileServices\profileServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use function Webmozart\Assert\Tests\StaticAnalysis\inArray;

class profileController extends Controller
{
    public function __construct(protected profileServices $profileServices)
    {

    }
    public function showEditProfile()
    {
        $user = User::where('id', Auth::id())->first();
        return view('index.profile.editProfileShow', compact('user'));
    }
    public function showEditPassword()
    {
        $user = User::where('id', Auth::id())->first();
        return view('index.profile.editPasswordShow', compact('user'));
    }
    public function editProfile(profileEditInfoReqeust $request){

        $data = $request->validated();
        $user = Auth::user();
        $editedUser = $this->profileServices->editProfile($user, $data);
        if($editedUser){
            return redirect(route('homepage'))->with('success', 'Profile updated successfully!');
        }else{
            return back()->with('fail', 'Something went wrong!');
        }
    }
    public function editPassword(profileEditPasswordReqeust $request)
    {
        $data = $request->validated();
        $user = User::where('id', Auth::id())->first();
        $editedUser = $this->profileServices->editPassword($user, $data);
        if($editedUser){
            return back()->with('success', 'Password updated successfully!');
        }else{
            return back()->with('fail', 'Something went wrong!');
        }
    }

    public function closeFriendShow()
    {
        $user = User::where('id', Auth::id())->first();
        $followings = followsModel::where('follower_id', $user->id)->with('userInfo')->get();
        $friends = closeFriendModel::where('user_id', $user->id)->with('userInfo')->get();
        $friendsCount = $friends->count();
        return view('index.closeFriend.closeFriend', compact('user','friends','followings','friendsCount'));
    }

    public function toggle()
    {
        $user = Auth::user();
        return $this->profileServices->CloseFriendToggle($user);
    }
}
