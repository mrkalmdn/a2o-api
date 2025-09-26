<?php

use App\Reports\ReportFactory;
use function PHPUnit\Framework\assertInstanceOf;

it('returns job_booking report', function () {
    $report = ReportFactory::make('job_booking');
    assertInstanceOf(App\Reports\JobBooking::class, $report);
});

it('returns conversion_funnel report', function () {
    $report = ReportFactory::make('conversion_funnel');
    assertInstanceOf(App\Reports\ConversionFunnel::class, $report);
});

it('throws exception for unknown report', function () {
    $this->expectException(Exception::class);
    ReportFactory::make('unknown');
});
