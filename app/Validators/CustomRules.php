<?php

namespace App\Validators;

use Ramsey\Uuid\Uuid;

class CustomRules
{
    public function valid_time(?string $str): bool
    {
        if ($str === null) {
            return true;
        }

        return (bool) preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $str);
    }

    public function uuid(string $str): bool
    {
        return Uuid::isValid($str);
    }
}