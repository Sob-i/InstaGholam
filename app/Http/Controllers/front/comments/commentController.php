<?php

namespace App\Http\Controllers\front\comments;

use App\Http\Controllers\Controller;
use App\Http\Requests\comments\commentCreateReqeust;
use App\Http\Requests\comments\commentReplyRequest;
use App\Models\comments\commentModel;
use App\Models\posts\postModel;
use App\Services\postsServices\postServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function Webmozart\Assert\Tests\StaticAnalysis\integer;

class commentController extends Controller
{
    public function __construct(protected postServices $postServices)
    {

    }
    public function getComments($postId)
    {
        return $this->postServices->GetComments($postId);
    }
    public function sendComment(commentCreateReqeust $request, $id){
        $data = [
            'comment' => $request->validated(),
            'post_id' => (int) $id,
            'user_id' => Auth::id(),
        ];
       return $this->postServices->AddComment($data);
    }
    public function sendCommentReply($postId , $commentId , commentReplyRequest $request){
        $data = [
            'reply' => $request->validated(),
            'post_id' => (int) $postId,
            'comment_id' => (int) $commentId,
            'user_id' => Auth::id(),
            'type' => 'reply'
        ];
       return $this->postServices->AddCommentReply($data);
    }
    public function getCommentReplies($postId , $commentId)
    {
        return $this->postServices->GetCommentReplies($postId , $commentId);
    }
    public function deleteComment($PostId , $commentId)
    {
        $data = [
            'comment_id' => $commentId,
            'post_id' => $PostId,
        ];
        return $this->postServices->DeleteComment($data);
    }
}
