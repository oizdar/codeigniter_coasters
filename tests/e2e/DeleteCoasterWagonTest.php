<?php

namespace e2e;

use App\Libraries\Coasters\CoastersService;
use App\Libraries\Coasters\CreateCoasterData;
use App\Libraries\Coasters\CreateCoasterWagonData;
use App\Models\Coaster;
use App\Models\Wagon;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use Ramsey\Uuid\Uuid;

/**
 * @internal
 */
final class DeleteCoasterWagonTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    private CoastersService $coastersService;
    private Coaster $coaster;
    private Wagon $wagon;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var CoastersService $coastersService */
        $coastersService =  service('coastersService');
        $this->coastersService = $coastersService;
        $this->coaster = $this->coastersService->create(
            new CreateCoasterData(
            1,
            16,
            1800,
            '8:00',
            '16:00'
            )
        );

        $this->wagon = $this->coastersService->addWagon($this->coaster, new CreateCoasterWagonData(100, 1.5));
    }

    public function testWagonDeleted(): void
    {
        $postData = [
            'number_of_places' => 100,
            'speed' => 1.5,
        ];

        $result = $this->withBody(json_encode($postData))
            ->delete('/api/coasters/' . $this->coaster->uuid . '/wagons/' . $this->wagon->uuid);

        $result->assertStatus(ResponseInterface::HTTP_NO_CONTENT);


        $coaster = $this->coastersService->get($this->coaster->uuid); //nice to prepare method in model to refresh data from db e.g. $coaster->fresh()
        $wagons = $coaster->getWagons();
        $this->assertEquals(0, $wagons->count());
    }

}
