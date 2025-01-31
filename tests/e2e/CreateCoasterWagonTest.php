<?php

namespace e2e;

use App\Libraries\Coasters\CoastersService;
use App\Libraries\Coasters\CreateCoasterData;
use App\Models\Coaster;
use App\Models\Wagon;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use Ramsey\Uuid\Uuid;

/**
 * @internal
 */
final class CreateCoasterWagonTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    private CoastersService $coastersService;
    private Coaster $coaster;

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
    }

    public function testWagonCreated(): void
    {
        $postData = [
            'number_of_places' => 100,
            'speed' => 1.5,
        ];

        $result = $this->withBody(json_encode($postData))
            ->post('/api/coasters/' . $this->coaster->uuid . '/wagons');

        $result->assertStatus(ResponseInterface::HTTP_CREATED);

        $result->assertJSONFragment([
            'message' => lang('Response.created', ['model' => 'Wagon']),
            'data' => $postData,
        ]);

        $coaster = $this->coastersService->get($this->coaster->uuid); //nice to prepare method in model to refresh data from db e.g. $coaster->fresh()
        $wagons = $coaster->getWagons();
        $this->assertEquals(1, $wagons->count());
        $uuid = Uuid::fromString(json_decode($result->getJSON())->data->uuid);

        $this->assertNotNull($wagons->find(fn(Wagon $wagon) => $wagon->uuid->equals($uuid)));



    }

}
