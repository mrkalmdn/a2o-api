<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportRequest;
use App\Reports\ReportFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function __invoke(ReportRequest $request)
    {
        $report = ReportFactory::make($request->type);
        $data = $report->export([
            'start' => $request->start,
            'end' => $request->end,
        ]);

        $output = fopen('php://temp', 'r+');

        if (!empty($data)) {
            fputcsv($output, array_keys($data[0]));
        }

        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $content = stream_get_contents($output);

        $type = $request->type;

        return new StreamedResponse(fn () => print($content), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$type.csv",
        ]);
    }
}
