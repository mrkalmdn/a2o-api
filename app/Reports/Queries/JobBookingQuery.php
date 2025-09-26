<?php

namespace App\Reports\Queries;

use App\Models\LogServiceTitanJob;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class JobBookingQuery
{
    public function execute(array $params = []): Collection
    {
        $start = data_get($params, 'start', now()->startOfMonth());
        $end = data_get($params, 'end', now()->endOfMonth());
        $marketIds = data_get($params, 'markets', [1, 2]);

        $cacheKey = "job_booking:" . md5(json_encode($params));

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($start, $end, $marketIds) {
            return LogServiceTitanJob::query()
                ->selectRaw("
                    DATE(start) as date,
                    market_id,
                    markets.name as market_name,
                    COUNT(*) as count
                ")
                ->join("markets", "markets.id", "=", "log_service_titan_jobs.market_id")
                ->whereBetween("start", [$start, $end])
                ->whereIn("market_id", $marketIds)
                ->groupBy("date", "market_id", "markets.name")
                ->orderBy("date")
                ->get();
        });
    }
}
