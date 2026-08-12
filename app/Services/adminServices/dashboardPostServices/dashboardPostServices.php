<?php

namespace App\Services\adminServices\dashboardPostServices;




use App\Models\posts\postModel;

class dashboardPostServices
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {

    }
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
    public function SearchPost(array $data)
    {
        $posts = $this->searchMethod($data['search'], $data['status']);
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
    public function ChangePostStatus(array $data)
    {
        $affectedPost = postModel::where('id', $data['id'])->update(['status' => $data['status']]);
        $status = $data['status'];
        if ($affectedPost) {
            $Count = +1;
            return response()->json([
                'success' => true,
                'message' => "$status post successfully",
                $status.'PostCount' => $Count,
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'something went wrong',
        ]);
    }

}

