<?php

namespace App\Http\Controllers\front\posts;

use App\Http\Controllers\Controller;
use App\Models\posts\postModel;
use App\Models\posts\postsSaveModel;
use App\Services\postsServices\postServices;
use Illuminate\Support\Facades\Cache;

class postsSaveController extends Controller
{
    public function __construct(protected postServices $postServices)
    {

    }

    public function toggle(postModel $post)
    {
        $user = auth()->user();
        return $this->postServices->SavePost($user, $post);
    }
}
