<?php

namespace App\Models;

use App\Helpers\Collection;
use App\Libraries\Coasters\CoasterStatusData;
use App\Libraries\Coasters\CreateCoasterData;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Coaster
{

    public readonly UuidInterface $uuid;

    /**
     * @var Collection<Wagon>
     */
    private Collection $wagons;

    public function __construct(
        ?UuidInterface $uuid = null,
        public int $number_of_staff,
        public int $number_of_clients,
        public int $route_length,
        public string $hours_from,
        public string $hours_to,
    ) {
        $this->uuid = $uuid ?? Uuid::uuid4();
        $this->wagons = new Collection();
    }

    /**
     * Create a Coaster from CreateCoasterData
     */
    public static function fromCreateCoasterData(CreateCoasterData $data): self
    {
        return new self(
            uuid: null,
            number_of_staff: $data->numberOfStaff,
            number_of_clients: $data->numberOfClients,
            route_length: $data->routeLength,
            hours_from: $data->hoursFrom,
            hours_to: $data->hoursTo
        );
    }

    /**
     * Serialize to an array for Redis storage
     */
    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid->toString(),
            'number_of_staff' => $this->number_of_staff,
            'number_of_clients' => $this->number_of_clients,
            'route_length' => $this->route_length,
            'hours_from' => $this->hours_from,
            'hours_to' => $this->hours_to,
            'wagons' => $this->wagons->map(fn(Wagon $wagon) => $wagon->toArray()),
        ];
    }

    /**
     * Deserialize from an array
     */
    public static function fromSerialized(array $data): self
    {
        $coaster = new self(
            uuid: Uuid::fromString($data['uuid']),
            number_of_staff: $data['number_of_staff'],
            number_of_clients: $data['number_of_clients'],
            route_length: $data['route_length'],
            hours_from: $data['hours_from'],
            hours_to: $data['hours_to']
        );


        foreach ($data['wagons'] ?? [] as $wagon) {
            $coaster->wagons->add(Wagon::fromSerialized($wagon));
        }

        return $coaster;
    }

    /**
     * @return Collection<Wagon>
     */
    public function getWagons(): Collection
    {
        return $this->wagons;
    }

    public function addWagon(Wagon $wagon): void
    {
        $this->wagons->add($wagon);
    }

    public function findWagonKey(UuidInterface $uuid): ?int
    {
        return $this->wagons->find(fn(Wagon $wagon) => $wagon->uuid->equals($uuid));
    }

    public function removeWagonByUuid(UuidInterface $wagonUuid): void
    {
        $index = $this->findWagonKey($wagonUuid);

        $this->wagons->remove($index);
    }


    public function getWagonByUuid(UuidInterface $wagonUuid): ?Wagon
    {
        $index = $this->findWagonKey($wagonUuid);

        return $this->wagons->get($index);
    }

    public function getServedPassengersDaily(): int
    {
        return array_sum($this->wagons->map(
            fn(Wagon $wagon) =>
                $wagon->servedPassengersDaily(
                    $this->route_length,
                    new \DateTimeImmutable($this->hours_from),
                    new \DateTimeImmutable($this->hours_to)
                )
            )
        );
    }
    public function getExpectedNumberOfWagons(): int
    {
        if($this->wagons->count() === 0) {
            return 0;
        }

        $averagePerWagon = $this->getServedPassengersDaily() / $this->wagons->count();

        return ceil($this->number_of_clients / $averagePerWagon);
    }

    public function getRequiredStaff(): int
    {
        $wagons = $this->wagons->count() > $this->getExpectedNumberOfWagons()
            ? $this->wagons->count()
            : $this->getExpectedNumberOfWagons();

        return 1 + ($wagons * 2);
    }

    public function getStatus(): CoasterStatusData
    {
        $status = new CoasterStatusData();

        if ($this->getRequiredStaff() > $this->number_of_staff) {
            $status->addError(lang('Messages.coaster.status.not_enough_staff',
                ['below' => $this->getRequiredStaff() - $this->number_of_staff]
            ));
        } elseif ($this->getRequiredStaff() < $this->number_of_staff) {
            $status->addError(lang('Messages.coaster.status.too_much_staff',
                ['above' => $this->number_of_staff - $this->getRequiredStaff()]
            ));
        }

        if ($this->wagons->count() === 0) {
            $status->addError(lang('Messages.coaster.status.no_wagons'));
        } elseif ($this->getServedPassengersDaily() > ($this->number_of_clients * 2)) {
            $status->addError(lang('Messages.coaster.status.too_many_wagons',
                [
                    'wagons' => $this->wagons->count() - $this->getExpectedNumberOfWagons(),
                ]
            ));
        }

        return $status;
    }
}