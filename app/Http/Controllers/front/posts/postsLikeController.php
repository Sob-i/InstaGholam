<?php

namespace App\Http\Controllers\front\posts;

use App\Http\Controllers\Controller;
use App\Models\posts\postModel;
use App\Models\posts\postsLikeModel;
use App\Services\postsServices\postServices;
use Illuminate\Support\Facades\Cache;


class postsLikeController extends Controller
{
    public function __construct(protected postServices $postServices)
    {

    }
    public function toggle(postModel $post)
    {
        $user = auth()->user();
        return $this->postServices->LikePost($user , $post);
    }
}
