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
            1,
            16,
            1800,
            '8:00',
            '16:00'
        );

        $coasterModel = $this->coastersService->save($createCoasterData);
        $this->assertEquals($coasterModel, $this->coastersService->get($coasterModel->uuid));

        $this->coastersService->delete($coasterModel->uuid);
        $this->assertNull($this->coastersService->get($coasterModel->uuid));
    }
}
