<?php

namespace unit;

use App\Libraries\Coasters\CoastersService;
use App\Libraries\Coasters\CreateCoasterData;
use App\Libraries\Coasters\CreateCoasterWagonData;
use App\Libraries\Coasters\UpdateCoasterData;
use App\Models\Wagon;
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

        $coasterModel = $this->coastersService->create($createCoasterData);
        $this->assertEquals($coasterModel, $this->coastersService->get($coasterModel->uuid));

        $updateCoasterData = new UpdateCoasterData(
            $coasterModel->uuid,
            2,
            32,
            3600,
            '9:00',
            '17:00'
        );

        $this->coastersService->update($coasterModel, $updateCoasterData);
        $this->assertEquals([
            'uuid' => $coasterModel->uuid,
            'number_of_staff' => 2,
            'number_of_clients' => 32,
            'route_length' => 3600,
            'hours_from' => '9:00',
            'hours_to' => '17:00',
            'wagons' => [],
        ], $this->coastersService->get($coasterModel->uuid)->toArray());


        $updateCoasterData = new UpdateCoasterData(
            $coasterModel->uuid,
            3,
            null,
            null,
            null,
            '19:00'
        );

        $this->coastersService->update($coasterModel, $updateCoasterData);
        $this->assertEquals([
            'uuid' => $coasterModel->uuid,
            'number_of_staff' => 3,
            'number_of_clients' => 32,
            'route_length' => 3600,
            'hours_from' => '9:00',
            'hours_to' => '19:00',
            'wagons' => [],
        ], $this->coastersService->get($coasterModel->uuid)->toArray());

        $createCoasterWagonData = new CreateCoasterWagonData(
            1,
            1,
        );
        $createCoasterWagonData2 = new CreateCoasterWagonData(
            1,
            1,
        );
        $this->coastersService->addWagon($coasterModel, $createCoasterWagonData);
        $this->coastersService->addWagon($coasterModel, $createCoasterWagonData2);
        $this->assertEquals(2, $this->coastersService->get($coasterModel->uuid)->getWagons()->count());
    }

    public function testCoasterEdgeCase(): void
    {
        $createCoasterData = new CreateCoasterData(
            1,
            2000,
            1000,
            '8:00',
            '16:00'
        );

        $coasterModel = $this->coastersService->create($createCoasterData);
        $this->assertEquals($coasterModel, $this->coastersService->get($coasterModel->uuid));

        $createCoasterWagonData = new CreateCoasterWagonData(
            5,
            1,
        );

        $this->coastersService->addWagon($coasterModel, $createCoasterWagonData);
        $coaster = $this->coastersService->get($coasterModel->uuid);

        $this->assertEquals(19, $coaster->getExpectedNumberOfWagons());
        $this->assertEquals(39, $coaster->getRequiredStaff());

        /** @var Wagon $wagon */
        $wagon = $coaster->getWagons()->get(0);
        $servedDaily = $wagon->servedPassengersDaily($coaster->route_length, new \DateTimeImmutable($coaster->hours_from), new \DateTimeImmutable($coaster->hours_to));

        $this->assertEquals(110, $servedDaily);
    }
}
