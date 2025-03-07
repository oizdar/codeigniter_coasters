<?php

namespace unit;

use App\Libraries\Redis\RedisService;
use CodeIgniter\Test\CIUnitTestCase;
use function React\Async\await;

class RedisServiceTest extends CIUnitTestCase
{
    private RedisService $redisService;

    private const  REDIS_KEY = 'test';
    private const  REDIS_KEY_2 = 'test2';

    private const  REDIS_VALUE = 'test';
    private const  REDIS_VALUE_2 = 'test2';
    private const REDIS_VALUE_3 = 'test3';

    protected function setUp(): void
    {
        parent::setUp();

        /** @var RedisService $redisService */
        $redisService =  service('redis');
        $this->redisService = $redisService;
    }

    public function testServiceActions(): void
    {
        $this->redisService->save(self::REDIS_KEY, self::REDIS_VALUE);
        $this->assertEquals(self::REDIS_VALUE, await($this->redisService->get(self::REDIS_KEY)));
        $this->redisService->save(self::REDIS_KEY_2, self::REDIS_VALUE_2);
        $this->assertEquals(self::REDIS_VALUE_2, await($this->redisService->get(self::REDIS_KEY_2)));

        $this->redisService->save(self::REDIS_KEY, self::REDIS_VALUE_3);
        $this->assertEquals(self::REDIS_VALUE_3, await($this->redisService->get(self::REDIS_KEY)));
        $this->assertEquals(self::REDIS_VALUE_2, await($this->redisService->get(self::REDIS_KEY_2)));

        $this->redisService->delete(self::REDIS_KEY);
        $this->assertNull(await($this->redisService->get(self::REDIS_KEY)));
        $this->assertEquals(self::REDIS_VALUE_2, await($this->redisService->get(self::REDIS_KEY_2)));
    }
}
