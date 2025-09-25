<?php

namespace App\Enums;

enum Event: int
{
    case JOB_TYPE_AND_ZIP_COMPLETED = 2;
    case APPOINTMENT_DATE_TIME_SELECTED = 3;
    case NEW_OR_REPEAT_CUSTOMER_SELECTED = 7;
    case TERMS_OF_SERVICE_LOADED = 623;
    case APPOINTMENT_CONFIRMED = 8;

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
