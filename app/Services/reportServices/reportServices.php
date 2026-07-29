<?php

namespace App\Services\reportServices;


use App\Models\posts\postsSaveModel;
use App\Models\reports\reportModel;
use App\Models\User;
use App\Models\user\followsModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class reportServices
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {

    }

    public function AddReport($data)
    {
        if ($data['reportableType'] == 'post')
        {
            $type = 'App\Models\posts\postModel';

        }elseif ($data['reportableType'] == 'comment'){

            $type = 'App\Models\comments\commentModel';
        }
        else{
            $type = 'App\Models\story\storyModel';
        }
        $createdReport = reportModel::create([
            'report_id' => $data['reporterUid'],
            'reported_user_id' => $data['reportedUid'],
            'reportable_type' => $type,
            'report_subject' => $data['reportSubject'],

        ]);

        if ($createdReport) {
            return response()->json([
                'status' => true,
                'message' => 'reported successfully.'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Something went wrong.'
        ]);
    }

}
