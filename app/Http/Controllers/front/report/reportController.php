<?php

namespace App\Http\Controllers\front\report;

use App\Http\Controllers\Controller;
use App\Http\Requests\reports\createReportRequest;
use App\Services\reportServices\reportServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class reportController extends Controller
{
    public function __construct(protected reportServices $reportServices)
    {

    }

    public function Report($uid , createReportRequest $request)
    {
        $request->validated();
        $data = [
            'reporterUid' => Auth::user()->id,
            'reportedUid' => $uid,
            'reportableId' => $request->get('id'),
            'reportableType' => $request->get('type'),
            'reportSubject' => $request->get('report-subject'),
        ];

        return $this->reportServices->AddReport($data);

    }
}
