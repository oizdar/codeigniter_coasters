<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Redis extends BaseConfig
{
    public string $defaultGroup = 'default';

    public array $default = [
        'host'     => '127.0.0.1',
        'port'     => 6379,
        'password' => null,
        'database' => 1,
    ];

    public array $tests = [
        'host'     => '127.0.0.1',
        'port'     => 6380,
        'password' => null,
        'database' => 2,
    ];

    public function __construct()
    {
        parent::__construct();

        // Override default group settings with environment variables
        $this->default['host'] = env('REDIS.DEFAULT.HOST', $this->default['host']);
        $this->default['port'] = env('REDIS.DEFAULT.PORT', $this->default['port']);
        $this->default['password'] = env('REDIS.DEFAULT.PASSWORD', $this->default['password']);
        $this->default['database'] = env('REDIS.DEFAULT.DATABASE', $this->default['database']);

        // Override tests group settings with environment variables
        $this->tests['host'] = env('REDIS.TESTS.HOST', $this->tests['host']);
        $this->tests['port'] = env('REDIS.TESTS.PORT', $this->tests['port']);
        $this->tests['password'] = env('REDIS.TESTS.PASSWORD', $this->tests['password']);
        $this->tests['database'] = env('REDIS.TESTS.DATABASE', $this->tests['database']);

        if (ENVIRONMENT === 'testing') {
            $this->defaultGroup = 'tests';
        }
    }
}
