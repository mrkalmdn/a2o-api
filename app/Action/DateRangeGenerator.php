<?php

namespace App\Action;

use Carbon\CarbonPeriodImmutable;
use Illuminate\Support\Carbon;

class DateRangeGenerator
{
    public function execute(Carbon|string $start, Carbon|string $end): array
    {
        $period = CarbonPeriodImmutable::create($start, $end);

        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        return $dates;
    }
}
