<?php

namespace App\Models;

use App\Libraries\Coasters\CreateCoasterWagonData;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Wagon
{

    public readonly UuidInterface $uuid;

    public function __construct(
        ?UuidInterface $uuid = null,
        public int $number_of_places,
        public float $speed,
    ) {
        $this->uuid = $uuid ?? Uuid::uuid4();
    }

    /**
     * Create a Coaster from CreateCoasterData
     */
    public static function fromCreateCoasterWagonData(CreateCoasterWagonData $data): self
    {
        return new self(
            uuid: null,
            number_of_places: $data->numberOfPlaces,
            speed: $data->speed,
        );
    }

    /**
     * Serialize to an array for Redis storage
     */
    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid->toString(),
            'number_of_places' => $this->number_of_places,
            'speed' => $this->speed,
        ];
    }

    /**
     * Deserialize from an array
     */
    public static function fromSerialized(array $data): self
    {
        return new self(
            uuid: Uuid::fromString($data['uuid']),
            number_of_places: $data['number_of_places'],
            speed: $data['speed'],
        );
    }
}