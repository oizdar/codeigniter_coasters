<?php

namespace e2e;

use App\Libraries\Coasters\CoastersService;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use Ramsey\Uuid\Uuid;

/**
 * @internal
 */
final class CreateCoasterTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    private CoastersService $coastersService;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var CoastersService $coastersService */
        $coastersService =  service('coastersService');
        $this->coastersService = $coastersService;
    }

    public function testPostCoastersCreated(): void
    {
        $postData = [
            'number_of_staff' => 16,
            'number_of_clients' => 60000,
            'route_length' => 1800,
            'hours_from' => '8:00',
            'hours_to' => '16:00',
        ];

        $result = $this->withBody(json_encode($postData))
            ->post('/api/coasters');

        $result->assertStatus(ResponseInterface::HTTP_CREATED);

        $result->assertJSONFragment([
            'message' => lang('Response.created', ['model' => 'Coaster']),
            'data' => $postData,
        ]);


        $uuid = Uuid::fromString(json_decode($result->getJSON())->data->uuid);
        $this->assertNotNull($this->coastersService->get($uuid));
    }


    public function postCoastersValidationFailureProvider(): array
    {
        return [
            [
                [],
                [
                    'number_of_staff' => lang('Validation.required', ['field' => 'number_of_staff']),
                    'number_of_clients' => lang('Validation.required', ['field' => 'number_of_clients']),
                    'route_length' => lang('Validation.required', ['field' => 'route_length']),
                    'hours_from' => lang('Validation.required', ['field' => 'hours_from']),
                    'hours_to' => lang('Validation.required', ['field' => 'hours_to']),
                ]
            ],
            [
                [
                'number_of_staff' => 'a',
                'number_of_clients' => 'b',
                'route_length' => 'c',
                'hours_from' => '',
                'hours_to' => '99:00',
                ],
                [
                    'number_of_staff' => lang('Validation.integer', ['field' => 'number_of_staff']),
                    'number_of_clients' => lang('Validation.integer', ['field' => 'number_of_clients']),
                    'route_length' => lang('Validation.integer', ['field' => 'route_length']),
                    'hours_from' => lang('Validation.required', ['field' => 'hours_from']),
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
            ->post('/api/coasters');

        $result->assertStatus(ResponseInterface::HTTP_BAD_REQUEST);

        $result->assertJSONExact([
            'message' => 'Validation failed.',
            'data' => $expectedResponse,
        ]);
    }

}
