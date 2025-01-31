<?php

namespace App\Libraries\Coasters;

use App\Libraries\Redis\RedisService;
use Ramsey\Uuid\UuidInterface;

class CoastersService
{

    public function __construct(private readonly RedisService $redisService)
    {
    }

    public function save(CreateCoasterData $data): void
    {
        $this->redisService->save('coasters_' . $data->uuid, $data);
    }

    public function get(UuidInterface $uuid): ?CreateCoasterData
    {
        return $this->redisService->get('coasters_' . $uuid->toString());
    }

    public function delete(UuidInterface $uuid): bool
    {
        return $this->redisService->delete('coasters_' . $uuid->toString());
    }
}