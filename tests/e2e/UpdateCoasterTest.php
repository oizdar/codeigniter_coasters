<?php

namespace e2e;

use App\Libraries\Coasters\CoastersService;
use App\Libraries\Coasters\CreateCoasterData;
use App\Models\Coaster;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use Ramsey\Uuid\Uuid;

/**
 * @internal
 */
final class UpdateCoasterTest extends CIUnitTestCase
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

    public function testPutCoasterNotFound()
    {
        $uuid = Uuid::uuid4();
        $result = $this->withBody(json_encode([]))
            ->put('/api/coasters/' . $uuid->toString());

        $result->assertStatus(ResponseInterface::HTTP_NOT_FOUND);

        $result->assertJSONExact([
            'message' => lang('Response.not_found', ['model' => 'Coaster', 'uuid' => $uuid->toString()]),
            'data' => null,
        ]);

    }
    public function testPutCoaster(): void
    {
        $postData = [
            'number_of_staff' => 16,
            'number_of_clients' => 60000,
            'route_length' => 1800,
            'hours_from' => '10:00',
            'hours_to' => '20:00',
        ];

        $result = $this->withBody(json_encode($postData))
            ->put('/api/coasters/' . $this->coaster->uuid);

        $result->assertStatus(ResponseInterface::HTTP_OK);

        $result->assertJSONFragment([
            'message' => lang('Response.updated', ['model' => 'Coaster']),
            'data' => $postData,
        ]);


    }


    public function postCoastersValidationFailureProvider(): array
    {
        return [
            [
                [
                'number_of_staff' => 'a',
                'number_of_clients' => 'b',
                'route_length' => 'c',
                'hours_to' => '99:00',
                ],
                [
                    'number_of_staff' => lang('Validation.integer', ['field' => 'number_of_staff']),
                    'number_of_clients' => lang('Validation.integer', ['field' => 'number_of_clients']),
                    'route_length' => lang('Validation.integer', ['field' => 'route_length']),
                    'hours_to' => lang('Validation.valid_time', ['field' => 'hours_to']),
                ]
            ],
            [
                [
                    'number_of_staff' => 0,
                    'number_of_clients' => 0,
                    'route_length' => 0,
                    'hours_from' => '8:00',
                    'hours_to' => '19:00',
                ],
                [
                    'number_of_staff' => lang('Validation.greater_than_equal_to', ['field' => 'number_of_staff', 'param' => '1']),
                    'number_of_clients' => lang('Validation.greater_than_equal_to', ['field' => 'number_of_clients', 'param' => '1']),
                    'route_length' => lang('Validation.greater_than_equal_to', ['field' => 'route_length', 'param' => '1']),
                ]
            ],
            [
                [
                    'number_of_staff' => 99999999,
                    'number_of_clients' => 99999999,
                    'route_length' => 99999999,
                    'hours_from' => '8:00',
                    'hours_to' => '19:00',
                ],
                [
                    'number_of_staff' => lang('Validation.less_than_equal_to', ['field' => 'number_of_staff', 'param' => '100']),
                    'number_of_clients' => lang('Validation.less_than_equal_to', ['field' => 'number_of_clients', 'param' => '1000000']),
                    'route_length' => lang('Validation.less_than_equal_to', ['field' => 'route_length', 'param' => '100000']),
                ]
            ]

        ];
    }

    /**
     * @dataProvider postCoastersValidationFailureProvider
     */
    public function testPostCoastersValidationFailure(array $postData, array $expectedResponse)
    {
        $result = $this->withBody(json_encode($postData))
            ->put('/api/coasters/' . $this->coaster->uuid);

        $result->assertStatus(ResponseInterface::HTTP_UNPROCESSABLE_ENTITY);

        $result->assertJSONExact([
            'message' => 'Validation failed.',
            'data' => $expectedResponse,
        ]);
    }

}
