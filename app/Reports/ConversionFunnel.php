<?php

namespace App\Reports;

use App\Enums\Event;
use App\Models\EventName;
use App\Models\LogEvent;
use App\Reports\Queries\ConversionFunnelQuery;
use Illuminate\Support\Facades\DB;

class ConversionFunnel implements Contracts\ReportBuilder
{
    public function __construct(private readonly ConversionFunnelQuery $query)
    {}

    public function build(array $params = []): array
    {
        [$events, $eventNames] = $this->query->execute($params);

        $labels = $eventNames->pluck('name')->unique()->toArray();
        $datasets = [];

        foreach ($events as $event) {
            $data = [];
            foreach ($eventNames as $index => $eventName) {
                $currentValue = $event->{"event_$index"} ?? 0;

                if ($index === 0) {
                    $data[] = 100;
                } else {
                    $previousIndex = $index - 1;

                    $previousValue = (int) data_get($event, "event_$previousIndex", 0);
                    $previousValue = $previousValue === 0 ? 1 : $previousValue;

                    $data[] = ($currentValue / $previousValue) * 100;
                }
            }
            $datasets[] = [
                'label' => "$event->market",
                'data'  => $data,
            ];
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }

    public function export(array $params = []): array
    {
        [$events, $eventNames] = $this->query->execute($params);


        $rows = [];
        foreach ($events as $event) {
            foreach ($eventNames as $index => $eventName) {
                $currentValue = $event->{"event_$index"} ?? 0;

                $percentage = $index === 0
                    ? 100
                    : (($event->{"event_" . ($index - 1)} ?? 1) > 0
                        ? ($currentValue / ($event->{"event_" . ($index - 1)} ?? 1)) * 100
                        : 0);

                $rows[] = [
                    'market' => $event->market,
                    'event' => $eventName->name,
                    'conversions_total' => $currentValue,
                    'conversions_percentage' => round($percentage, 2),
                ];
            }
        }

        return $rows;
    }
}
