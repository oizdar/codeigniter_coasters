<?php

namespace App\Libraries\Redis;

use Predis\Client;

class RedisService
{
    public function __construct(private Client $client)
    {
    }

    /**
     * Save data to Redis.
     * @param string $key
     * @param mixed $data
     * @param int $ttl Time-to-live in seconds
     */
    public function save(string $key, $data, int $ttl = 3600): bool
    {
        $serializedData = serialize($data); // Serialize the model/data
        $this->client->setex($key, $ttl, $serializedData);
        return true;
    }

    /**
     * Retrieve data from Redis.
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key)
    {
        $data = $this->client->get($key);
        return $data ? unserialize($data) : null; // Unserialize the data
    }

    /**
     * Delete data from Redis.
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool
    {
        return $this->client->del([$key]) > 0;
    }

    /**
     * Check if a key exists in Redis.
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool
    {
        return $this->client->exists($key) > 0;
    }
}