<?php

namespace App\Libraries\Coasters;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

readonly class UpdateCoasterData
{
    public function __construct(
        public UuidInterface $uuid,
        public ?int    $numberOfStaff,
        public ?int    $numberOfClients,
        public ?int    $routeLength,
        public ?string $hoursFrom,
        public ?string $hoursTo
    ) {
    }

    public static function fromArray(array $data): self
    {

        return new self(
            uuid: $data['uuid'] ? Uuid::fromString($data['uuid']) : null,
            numberOfStaff: $data['number_of_staff'] ?? null,
            numberOfClients: $data['number_of_clients'] ?? null,
            routeLength: $data['route_length'] ?? null,
            hoursFrom: $data['hours_from'] ?? null,
            hoursTo: $data['hours_to'] ?? null
        );
    }
}