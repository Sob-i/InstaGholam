<?php

namespace App\Http\Controllers\front\story;

use App\Http\Controllers\Controller;
use App\Services\storiesServices\storiesServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class storyController extends Controller
{
    public function __construct(protected storiesServices $storyServices)
    {

    }

    public function newStory(Request $request)
    {
        $user = Auth::user();
        return $this->storyServices->NewStory($request,$user);
    }
}
