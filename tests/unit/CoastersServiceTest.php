<?php

namespace unit;

use App\Libraries\Coasters\CoastersService;
use App\Libraries\Coasters\CreateCoasterData;
use CodeIgniter\Test\CIUnitTestCase;

class CoastersServiceTest extends CIUnitTestCase
{
    private CoastersService $coastersService;



    protected function setUp(): void
    {
        parent::setUp();

        /** @var CoastersService $coastersService */
        $coastersService =  service('coastersService');
        $this->coastersService = $coastersService;
    }

    public function testServiceActions(): void
    {
        $createCoasterData = new CreateCoasterData(
            null,
            1,
            16,
            1800,
            '8:00',
            '16:00'
        );

        $this->assertTrue($createCoasterData->uuid !== null);
        $this->coastersService->save($createCoasterData);
        $this->assertEquals($createCoasterData, $this->coastersService->get($createCoasterData->uuid));

        $this->coastersService->delete($createCoasterData->uuid);
        $this->assertNull($this->coastersService->get($createCoasterData->uuid));
    }
}
