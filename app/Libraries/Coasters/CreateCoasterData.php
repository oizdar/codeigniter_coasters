<?php

namespace App\Libraries\Coasters;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class CreateCoasterData
{


    public function __construct(
        public ?UuidInterface $uuid,
        public readonly int    $numberOfStaff,
        public readonly int    $numberOfClients,
        public readonly int    $routeLength,
        public readonly string $hoursFrom,
        public readonly string $hoursTo
    ) {
        if($this->uuid === null) {
            $this->uuid = Uuid::uuid4();
        }
    }

    public static function fromArray(array $data): self
    {

        return new self(
            uuid: Uuid::isValid($data['uuid'] ?? '') ? Uuid::fromString($data['uuid']) : null,
            numberOfStaff: $data['number_of_staff'],
            numberOfClients: $data['number_of_clients'],
            routeLength: $data['route_length'],
            hoursFrom: $data['hours_from'],
            hoursTo: $data['hours_to']
        );
    }
}