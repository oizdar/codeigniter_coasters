<?php

namespace App\Libraries\Coasters;

use App\Libraries\Redis\RedisService;
use App\Models\Coaster;
use App\Models\Wagon;
use Ramsey\Uuid\UuidInterface;
use function React\Async\await;

class CoastersService
{

    public function __construct(private readonly RedisService $redisService)
    {
    }

    public function create(CreateCoasterData $data): Coaster
    {
        $coaster = Coaster::fromCreateCoasterData($data);
        $this->redisService->save('coasters_' . $coaster->uuid, $coaster->toArray());

        return $coaster;
    }

    public function update(Coaster $coaster, UpdateCoasterData $data): Coaster
    {
        $coaster->number_of_staff = $data->numberOfStaff ?? $coaster->number_of_staff;
        $coaster->number_of_clients = $data->numberOfClients ?? $coaster->number_of_clients;
        $coaster->route_length = $data->routeLength ?? $coaster->route_length;
        $coaster->hours_from = $data->hoursFrom ?? $coaster->hours_from;
        $coaster->hours_to = $data->hoursTo ?? $coaster->hours_to;

        $this->redisService->save('coasters_' . $coaster->uuid, $coaster->toArray());

        return $coaster;
    }

    public function get(UuidInterface $uuid): ?Coaster
    {
        $data = await($this->redisService->get('coasters_' . $uuid->toString()));

        return $data ? Coaster::fromSerialized($data) : null;

    }

    public function addWagon(Coaster $coaster, CreateCoasterWagonData $data): Wagon
    {
        $wagon = Wagon::fromCreateCoasterWagonData($data);
        $coaster->addWagon($wagon);

        $this->redisService->save('coasters_' . $coaster->uuid, $coaster->toArray());

        return $wagon;
    }

    public function deleteWagon(Coaster $coaster, UuidInterface $wagonUuid): void
    {
        $coaster->removeWagonByUuid($wagonUuid);

        $this->redisService->save('coasters_' . $coaster->uuid, $coaster->toArray());
    }
}