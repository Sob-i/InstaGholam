<?php

namespace App\Http\Controllers\front\profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\highlights\createHighlightRequest;
use App\Http\Requests\profile\profileEditInfoReqeust;
use App\Http\Requests\profile\profileEditPasswordReqeust;
use App\Models\closeFriend\closeFriendModel;
use App\Models\comments\commentModel;
use App\Models\highlights\highlightsModel;
use App\Models\posts\postModel;
use App\Models\story\storyModel;
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
    public function showFollowers($username)
    {
        $userId = User::where('username', $username)->first()->id;
        $followers = $this->profileServices->showFollowers($userId);
        if ($followers)
        {
            return response()->json([
                'status' => true,
                'data' => $followers
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Something went wrong!'
        ]);
    }
    public function showFollowings($username)
    {
        $userId = User::where('username', $username)->first()->id;
        $followings = $this->profileServices->showFollowings($userId);
        if ($followings)
        {
            return response()->json([
                'status' => true,
                'data' => $followings
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Something went wrong!'
        ]);
    }
    public function showHighlights()
    {
        $highlights = storyModel::where('user_id', Auth::id())->where('status','archived')->orderBy('created_at','desc')->get();
        return view('index.highlights.highlights', compact('highlights'));
    }
    public function createHighlight(createHighlightRequest $request)
    {
        $validated = $request->validated();

        $user = strstr(Auth()->user()->email, '@', true);

        $cover = $this->profileServices->CreateHighlightCoverAndMove($validated['cover'], $validated['title'], $user);

        $data = [
            'title' => $validated['title'],
            'cover' => $cover,
            'stories' => $validated['stories'],
        ];

        $createdHighlights = $this->profileServices->CreateHighlight($data);

        if ($createdHighlights) {
            return response()->json([
                'status' => true,
                'message' => 'Highlight created successfully!'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Something went wrong!'
        ]);
    }
    public function showHighlight($username ,$highlight)
    {
        $items = highlightsModel::where('cover', $highlight)->orderBy('created_at','desc')->with('stories.user')->get();
        if ($items)
        {
            return response()->json([
                'status' => true,
                'data' => $items
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Something went wrong!'
        ]);
    }
}
