<?php

namespace App\Enums;

enum Report: string
{
    case JOB_BOOKINGS = 'job_booking';
    case CONVERSION_FUNNEL = 'conversion_funnel';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
