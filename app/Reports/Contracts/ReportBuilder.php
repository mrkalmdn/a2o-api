<?php

namespace App\Reports\Contracts;

interface ReportBuilder
{
    public function build(array $params = []): array;

    public function export(array $params = []): array;
}
