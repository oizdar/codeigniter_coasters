<?php

namespace App\Libraries\Coasters;

readonly class CreateCoasterWagonData
{
    public function __construct(
        public int $numberOfPlaces,
        public float $speed,
    ) {
    }

    public static function fromArray(array $data): self
    {

        return new self(
            $data['number_of_places'],
            $data['speed'],
        );
    }
}