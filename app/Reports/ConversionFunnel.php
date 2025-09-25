<?php

namespace App\Reports;

use App\Enums\Event;
use App\Models\EventName;
use App\Models\LogEvent;
use Illuminate\Support\Facades\DB;

class ConversionFunnel implements Contracts\ReportBuilder
{

    public function build(array $params = []): array
    {
        $start = data_get($params, 'start', now()->startOfMonth());
        $end = data_get($params, 'end', now()->endOfMonth());

        $ids = Event::values();
        $eventNames = EventName::query()
            ->whereIn('id', $ids)
            ->orderByRaw('FIELD(id, ' . implode(',', $ids) . ')')
            ->get();

        $selects = collect($eventNames)->map(function ($eventName, $index) {
            $key = $eventName->id;
            return "SUM(CASE WHEN event_name_id = $key THEN 1 ELSE 0 END) as event_$index";
        })->implode(', ');

        $event = LogEvent::query()
            ->selectRaw($selects)
            ->whereBetween(DB::raw('DATE(created_at)'), [$start, $end])
            ->first();

        $labels = [];
        $data = [];

        foreach ($eventNames as $index => $eventName) {
            $currentValue = $event->{"event_$index"} ?? 0;
            $labels[] = "$eventName->name ($currentValue)";

            if ($index === 0) {
                $data[] = 100;
            } else {
                $previousIndex = $index - 1;
                $previousValue = $event->{"event_$previousIndex"} ?? 1;

                $data[] = ($currentValue / $previousValue) * 100;
            }
        }

        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Conversion Funnel',
                'data' => $data,
            ]]
        ];
    }
}
