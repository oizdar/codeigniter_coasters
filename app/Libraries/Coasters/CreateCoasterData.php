<?php

namespace App\Libraries\Coasters;

readonly class CreateCoasterData
{
    public function __construct(
        public int    $numberOfStaff,
        public int    $numberOfClients,
        public int    $routeLength,
        public string $hoursFrom,
        public string $hoursTo
    ) {
    }

    public static function fromArray(array $data): self
    {

        return new self(
            numberOfStaff: $data['number_of_staff'],
            numberOfClients: $data['number_of_clients'],
            routeLength: $data['route_length'],
            hoursFrom: $data['hours_from'],
            hoursTo: $data['hours_to']
        );
    }
}