<?php

namespace App\Http\Controllers\front\posts;

use App\Http\Controllers\Controller;
use App\Http\Requests\posts\postCreateReqeust;
use App\Models\closeFriend\closeFriendModel;
use App\Models\comments\commentModel;
use App\Models\User;
use App\Models\user\followsModel;
use App\Services\postsServices\postServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\posts\postModel;

class postController extends Controller
{

    public function __construct(protected postServices $postServices)
    {

    }

    public function showPost($post_id){

        $user = User::where('id', Auth::id())->first();
        $post = PostModel::where('id', $post_id)->where('status','active')->with('user')->first();
        $postWithInfo = $this->postServices->ShowSinglePost($user,$post);

        return view('index.postSingle.single', compact( 'user','postWithInfo','post'));
    }
    public function createPost(postCreateReqeust $request){
           $user = Auth::user();
           $data = [
               'postData' =>$request->validated(),
               'user' => $user,
               'file' => $request->file('uploadFile'),
           ];
           $uploadedPost = $this->postServices->CreatePost($data);

            if($uploadedPost){
                User::where('id',$user->id)->increment('posts');
                return back()->with('success', 'post uploaded successfully!');
            }else{
                return back()->with('fail', 'Something went wrong!');
            }
    }

}
