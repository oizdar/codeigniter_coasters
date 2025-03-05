<?php

namespace App\Validators;

use App\Models\Wagon;
use Ramsey\Uuid\Uuid;

class CustomRules
{
    public function valid_time(?string $value): bool
    {
        if ($value === null) {
            return true;
        }

        return (bool) preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $value);
    }

    public function uuid(string $value): bool
    {
        return Uuid::isValid($value);
    }

    public function validate_coaster_wagon_number_of_places(?int $value, string $coasterUuid): bool
    {
        if ($value === null) {
            return true;
        }

        $wagon = $this->getWagonForValidation($coasterUuid);

        if($wagon === null) {
            return true;
        }
        return $wagon?->number_of_places === $value;
    }

    public function validate_coaster_wagon_speed(?float $value, string $coasterUuid): bool
    {
        if ($value === null) {
            return true;
        }

        $wagon = $this->getWagonForValidation($coasterUuid);

        if($wagon === null) {
            return true;
        }

        return $wagon?->speed === $value;
    }

    private function getWagonForValidation(string $coasterUuid): ?Wagon
    {
        $coastersService = service('CoastersService');
        if(Uuid::isValid($coasterUuid)) {
            $coaster = $coastersService->get(Uuid::fromString($coasterUuid));
            foreach ($coaster->getWagons() as $wagon) {
                return $wagon;
            }
        }

        return null;
    }
}