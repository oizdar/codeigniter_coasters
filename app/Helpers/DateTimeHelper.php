<?php

namespace App\Helpers;

class DateTimeHelper
{
    public static function intervalInSeconds(\DateInterval $interval): int
    {
        return ($interval->d * 86400)
            + ($interval->h * 3600)
            + ($interval->i * 60)
            + $interval->s;
    }
}