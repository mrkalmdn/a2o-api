<?php

use App\Action\DateRangeGenerator;
use function PHPUnit\Framework\assertCount;

it('can generate date range', function () {
    $start = "2025-09-01";
    $end = "2025-09-30";

    $dates = (new DateRangeGenerator())->execute($start, $end);

    assertCount(30, $dates);
});
