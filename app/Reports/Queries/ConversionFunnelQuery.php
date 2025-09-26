<?php

namespace App\Reports\Queries;

use App\Enums\Event;
use App\Models\EventName;
use App\Models\LogEvent;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ConversionFunnelQuery
{
    public function execute(array $params = []): array
    {
        $start = data_get($params, 'start', now()->startOfMonth());
        $end = data_get($params, 'end', now()->endOfMonth());
        $marketIds = data_get($params, 'market_ids', [1, 2, 64]);

        $cacheKey = "conversion_funnel:" . md5(json_encode($params));

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($start, $end, $marketIds) {
            $ids = Event::values();
            $eventNames = EventName::query()
                ->whereIn('id', $ids)
                ->orderByRaw('FIELD(id, ' . implode(',', $ids) . ')')
                ->get();

            $selects = collect($eventNames)->map(function ($eventName, $index) {
                $key = $eventName->id;
                return "SUM(CASE WHEN event_name_id = $key THEN 1 ELSE 0 END) as event_$index";
            })->implode(', ');

            $selects = "markets.name as market, $selects";

            $events = LogEvent::query()
                ->selectRaw($selects)
                ->whereIn('market_id', $marketIds)
                ->join("markets", "markets.id", "=", "log_events.market_id")
                ->whereBetween(DB::raw('DATE(log_events.created_at)'), [$start, $end])
                ->groupBy('market')
                ->get();

            return [
                $events,
                $eventNames,
            ];
        });
    }
}
