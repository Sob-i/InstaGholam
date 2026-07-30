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
        $exists = reportModel::where('reporter_id', $data['reporterUid'])
            ->where('reportable_id', $data['reportableId'])
            ->where('reportable_type', $type)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'You have already reported this.'
            ]);
        }

        $createdReport = reportModel::create([
            'reporter_id' => $data['reporterUid'],
            'reported_user_id' => $data['reportedUid'],
            'reportable_id' => $data['reportableId'],
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
