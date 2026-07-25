<?php

namespace App\Http\Controllers\admin\adminDashboard;

use App\Http\Controllers\Controller;
use App\Models\posts\postModel;
use Illuminate\Http\Request;

class postsDashboardController extends Controller
{
    private function searchMethod($search , $status)
    {
        return postModel::where('status', $status)
            ->where(function($query) use ($search) {
                $query->where('post_tags', 'like', '%' . $search . '%')
                    ->orWhere('post_caption', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function($userQuery) use ($search) {
                        $userQuery->where('username', 'like', '%' . $search . '%');
                    });
            })
            ->with('user')
            ->latest()
            ->get();
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status', 'active');

        $posts = $this->searchMethod($search , $status);

        if (!$posts) {
            return response()->json([
                'success' => false,
                'message' => 'nothing found',
            ]);
        }

        return response()->json([
            'success' => true,
            'posts' => $posts,
        ]);
    }
    public function searchInFlagged(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status', 'flagged');

        $posts = $this->searchMethod($search , $status);

            if (!$posts) {
                return response()->json([
                    'success' => false,
                    'message' => 'nothing found',
                ]);
            }

            return response()->json([
                'success' => true,
                'posts' => $posts,
            ]);
    }
    public function searchInHidden(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status', 'hidden');

        $posts = $this->searchMethod($search , $status);

        if (!$posts) {
            return response()->json([
                'success' => false,
                'message' => 'nothing found',
            ]);
        }

        return response()->json([
            'success' => true,
            'posts' => $posts,
        ]);
    }
    private function changeStatus($postId,$status)
    {
        return postModel::where('id', $postId)->update(['status' => $status]);
    }

    public function postStatusToFlagged($postId)
    {
        $affectedPost = $this->changeStatus($postId, 'flagged');
        $flaggedCount = +1;
        if ($affectedPost) {
            return response()->json([
                'success' => true,
                'message' => 'Flagged post successfully',
                'flaggedPostCount' => $flaggedCount,
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'something went wrong',
        ]);
    }
    public function postStatusToHidden($postId)
    {
        $affectedPost = $this->changeStatus($postId, 'hidden');
        $hiddenCount = +1;
        if ($affectedPost) {
            return response()->json([
                'success' => true,
                'hiddenPostCount' => $hiddenCount,
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'something went wrong',
        ]);
    }
    public function postStatusToActive($postId)
    {
        $affectedPost = $this->changeStatus($postId, 'active');
        $activeCount = +1;
        if ($affectedPost) {
            return response()->json([
                'success' => true,
                'activePostCount' => $activeCount,
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'something went wrong',
        ]);
    }
}
