<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportRequest;
use App\Reports\ReportFactory;

class ReportController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(ReportRequest $request)
    {
        $report = ReportFactory::make($request->type);
        $data = $report->build([
            'start' => $request->start,
            'end' => $request->end,
        ]);

        return response()->json($data);
    }
}
