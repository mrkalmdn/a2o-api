<?php

namespace App\Reports;

use App\Reports\Contracts\ReportBuilder;
use Throwable;
use Exception;

class ReportFactory
{
    /**
     * @throws Throwable
     */
    public static function make(string $report): ReportBuilder
    {
        $class = str($report)->studly()->prepend('App\\Reports\\')->value();

        throw_if(!class_exists($class), new Exception("Unknown report type: $report"));

        return app($class);
    }
}
