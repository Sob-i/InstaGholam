<?php

namespace App\Http\Controllers\front\explore;

use App\Http\Controllers\Controller;
use App\Models\posts\postModel;
use App\Models\posts\postsSaveModel;
use App\Models\User;
use App\Models\user\followsModel;
use App\Services\exploreServices\exploreServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class exploreController extends Controller
{
    public function __construct(protected exploreServices $exploreServices)
    {

    }
    public function showExplore(Request $request){
        $user = Auth::user();
        $data = $this->exploreServices->Explore($request,$user);
        return view('index.explore.explore',compact('data'));
    }
    public function search(Request $request)
    {
       return $this->exploreServices->ExploreSearch($request);
    }
}
