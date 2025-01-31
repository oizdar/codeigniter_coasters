<?php

namespace Config;

use App\Libraries\Coasters\CoastersService;
use App\Libraries\Redis\RedisService;
use CodeIgniter\Config\BaseService;
use Predis\Client;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{

    /**
     * @param bool $getShared
     * @return Client
     */
    public static function redisClient(bool $getShared = true): object
    {
        if ($getShared) {
            return static::getSharedInstance('redisClient');
        }

        /** @var Redis $config */
        $config = config('Redis');
        $group = $config->defaultGroup;
        $settings = $config->{$group};

        // Initialize Predis or any Redis connection library
        return new Client([
            'scheme' => 'tcp',
            'host' => $settings['host'],
            'port' => $settings['port'],
            'password' => $settings['password'],
            'database' => $settings['database'],
        ]);
    }

    /**
     * @return RedisService
     */
    public static function redis(bool $getShared = true): object
    {
        if ($getShared) {
            return static::getSharedInstance('redis');
        }

        /** @var Client $redisClient */
        $redisClient = service('redisClient');

        return new RedisService($redisClient);
    }

    /**
     * @return CoastersService
     */
    public static function coastersService(bool $getShared = true): object
    {
        if ($getShared) {
            return static::getSharedInstance('coastersService');
        }

        /** @var RedisService $redis */
        $redis = service('redis');
        return new CoastersService($redis);
    }
}
