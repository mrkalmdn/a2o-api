<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportRequest;
use App\Reports\ReportFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
            'markets' => $request->markets ?? [],
        ]);

        return response()->json($data);
    }
}
