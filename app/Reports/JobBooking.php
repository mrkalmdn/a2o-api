<?php

namespace App\Reports;

use App\Action\DateRangeGenerator;
use App\Models\LogServiceTitanJob;
use App\Reports\Contracts\ReportBuilder;

class JobBooking implements Contracts\ReportBuilder
{

    public function build(array $params = []): array
    {
        $start = data_get($params, 'start', now()->startOfMonth());
        $end = data_get($params, 'end', now()->endOfMonth());

        $logs = LogServiceTitanJob::query()
            ->selectRaw("
                DATE(start) as date,
                market_id,
                markets.name as market_name,
                COUNT(*) as count
            ")
            ->join("markets", "markets.id", "=", "log_service_titan_jobs.market_id")
            ->whereBetween("start", [$start, $end])
            ->groupBy("date", "market_id", "markets.name")
            ->orderBy("date")
            ->get();

        $dates = (new DateRangeGenerator())->execute($start, $end);
        $markets = $logs->pluck('market_name')->unique()->values()->toArray();

        $lookup = [];
        foreach ($logs as $log) {
            $lookup[$log->date][$log->market_name] = $log->count;
        }

        $datasets = [];
        foreach ($markets as $market) {
            $data = [];
            foreach ($dates as $date) {
                $data[] = $lookup[$date][$market] ?? 0;
            }
            $datasets[] = [
                'label' => $market,
                'data' => $data,
                'fill' => false,
                'tension' => 0.3,
            ];
        }

        return [
            'labels' => $dates,
            'datasets' => $datasets,
        ];
    }
}
