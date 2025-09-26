<?php

namespace App\Reports;

use App\Action\DateRangeGenerator;
use App\Models\LogServiceTitanJob;
use App\Reports\Queries\JobBookingQuery;
use Illuminate\Support\Facades\Cache;

class JobBooking implements Contracts\ReportBuilder
{
    public function __construct(private readonly JobBookingQuery $query)
    {}

    public function build(array $params = []): array
    {
        $start = data_get($params, 'start', now()->startOfMonth());
        $end = data_get($params, 'end', now()->endOfMonth());

        $logs = $this->query->execute($params);

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

    public function export(array $params = []): array
    {
        $start = data_get($params, 'start', now()->startOfMonth());
        $end = data_get($params, 'end', now()->endOfMonth());

        $logs = $this->query->execute($params);

        return $logs->map(fn ($log) => [
            'market'   => $log->market_name,
            'date'     => $log->date,
            'bookings' => $log->count,
        ])->toArray();
    }
}
