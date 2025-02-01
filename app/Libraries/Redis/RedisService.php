<?php

namespace App\Libraries\Redis;

use Clue\React\Redis\RedisClient;
use React\Promise\Promise;

class RedisService
{
    public function __construct(private RedisClient $client)
    {
    }

    public function save(string $key, $data): Promise
    {
        $serializedData = serialize($data);

       return $this->client->set($key, $serializedData);
    }

    public function get(string $key)
    {
        $data = $this->client->get($key);
        return $data ? unserialize($data) : null;
    }

    public function delete(string $key): bool
    {
        return $this->client->del([$key]) > 0;
    }

    public function exists(string $key): bool
    {
        return $this->client->exists($key) > 0;
    }
}