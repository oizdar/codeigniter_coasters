<?php

namespace App\Validators;

class CustomRules
{
    public function valid_time(string $str): bool
    {
        return (bool) preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $str);
    }
}