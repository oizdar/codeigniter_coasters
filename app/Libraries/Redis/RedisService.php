<?php

namespace App\Libraries\Redis;

use Clue\React\Redis\RedisClient;
use React\Promise\PromiseInterface;

class RedisService
{
    public function __construct(private RedisClient $client)
    {
    }

    public function save(string $key, $data): PromiseInterface
    {
        $serializedData = serialize($data);

        return $this->client->set($key, $serializedData)->then(
            function () {
                // log success
            }, function ($error) {
                // log error
        });
    }

    public function get(string $key): PromiseInterface
    {
        return $this->client->get($key)->then(
            function ($value) {
                return $value ? unserialize($value) : null;
                // log success
            }, function ($error) {
                // log error
            });
    }

    public function delete(string $key): PromiseInterface
    {
        return $this->client->del([$key])->then(
            function ($value) {
                return $value ? unserialize($value) : null;
                // log success
            }, function ($error) {
            // log error
        });
    }

    public function exists(string $key): PromiseInterface
    {
        return $this->client->exists($key)->then(
            function ($value) {
                // log success
                return $value > 0;
            }, function ($error) {
            // log error
        });
    }
}