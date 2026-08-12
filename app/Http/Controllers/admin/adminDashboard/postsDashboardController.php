<?php

namespace App\Http\Controllers\admin\adminDashboard;

use App\Http\Controllers\Controller;
use App\Models\posts\postModel;
use App\Services\adminServices\dashboardPostServices\dashboardPostServices;
use Illuminate\Http\Request;

class postsDashboardController extends Controller
{
    public function __construct(protected dashboardPostServices $dashboardPostServices)
    {

    }
    public function search(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $data = [
            'search' => $search,
            'status' => $status
        ];

        return $this->dashboardPostServices->SearchPost($data);
    }
    public function postStatusChange($postId , $status)
    {
        $data = [
            'id' => $postId,
            'status' => $status
        ];

        return $this->dashboardPostServices->ChangePostStatus($data);
    }
}
