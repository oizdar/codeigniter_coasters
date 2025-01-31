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

    public function createCoasterWagonValidionErrorsProvider(): array
    {
        return [
            [
                [],
                [
                    'number_of_places' => lang('Validation.required', ['field' => 'number_of_places']),
                    'speed' => lang('Validation.required', ['field' => 'speed']),
                ],
            ],
            [
                [
                    'number_of_places' => 'a',
                    'speed' => '1.1.5',
                ],
                [
                    'number_of_places' => lang('Validation.integer', ['field' => 'number_of_places']),
                    'speed' => lang('Validation.numeric', ['field' => 'speed']),
                ]
            ],
            [
                [
                    'number_of_places' => 0,
                    'speed' => 1.5,
                ],
                [
                    'number_of_places' => lang('Validation.greater_than_equal_to', ['field' => 'number_of_places', 'param' => '1']),
                ]
            ],
            [
                [
                    'number_of_places' => 101,
                    'speed' => 1.5,
                ],
                [
                    'number_of_places' => lang('Validation.less_than_equal_to', ['field' => 'number_of_places', 'param' => '100']),
                ]
            ],
            [
                [
                    'number_of_places' => 100,
                    'speed' => 0,
                ],
                [
                    'speed' => lang('Validation.greater_than', ['field' => 'speed', 'param' => '0']),
                ]
            ],
            [
                [
                    'number_of_places' => 100,
                    'speed' => 1010,
                ],
                [
                    'speed' => lang('Validation.less_than_equal_to', ['field' => 'speed', 'param' => '100']),
                ]
            ],
        ];
    }

    /**
     * @dataProvider createCoasterWagonValidionErrorsProvider
     */
    public function testCreateCoasterWagonValidationErrors($data, $expectedErrors): void
    {
        $result = $this->withBody(json_encode($data))
            ->post('/api/coasters/' . $this->coaster->uuid . '/wagons');

        $result->assertStatus(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY);

        $result->assertJSONFragment([
            'message' => lang('Validation.failed'),
            'data' => $expectedErrors,
        ]);
    }

}
