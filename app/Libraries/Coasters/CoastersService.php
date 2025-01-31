<?php

namespace App\Libraries\Coasters;

use App\Libraries\Redis\RedisService;
use App\Models\Coaster;
use Ramsey\Uuid\UuidInterface;

class CoastersService
{

    public function __construct(private readonly RedisService $redisService)
    {
    }

    public function save(CreateCoasterData $data): Coaster
    {
        $coaster = Coaster::fromCreateCoasterData($data);
        $this->redisService->save('coasters_' . $coaster->uuid, $coaster->toArray());

        return $coaster;
    }

    public function get(UuidInterface $uuid): ?Coaster
    {
        $data = $this->redisService->get('coasters_' . $uuid->toString());

        return $data ? Coaster::fromSerialized($data) : null;

    }

    public function delete(UuidInterface $uuid): bool
    {
        return $this->redisService->delete('coasters_' . $uuid->toString());
    }
}