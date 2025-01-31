<?php

namespace App\Controllers\Coasters;

use App\Controllers\BaseController;
use App\Helpers\ResponseHelper;
use App\Libraries\Coasters\CoastersService;
use App\Libraries\Coasters\UpdateCoasterData;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Ramsey\Uuid\Uuid;

class UpdateCoaster extends BaseController
{
    private array $rules = [
        'uuid' => [
            'required',
            'uuid',
        ],
        'number_of_staff' => [
            'permit_empty',
            'integer',
            'greater_than_equal_to[1]',
            'less_than_equal_to[100]',
        ],
        'number_of_clients' => [
            'permit_empty',
            'integer',
            'greater_than_equal_to[1]',
            'less_than_equal_to[1000000]',

        ],
        'route_length' => [
            'permit_empty',
            'integer',
            'greater_than_equal_to[1]',
            'less_than_equal_to[100000]',
        ],
        'hours_from' => [
            'permit_empty',
            'valid_time',
        ],
        'hours_to' => [
            'permit_empty',
            'valid_time',
        ],
    ];

    private CoastersService $coastersService;

    public function __construct()
    {
        $this->coastersService = Services::coastersService();
    }

    public function __invoke(string $uuid): ResponseInterface
    {
        $requestData = $this->request->getJSON(true);
        $requestData['uuid'] = $uuid;

        if(!$this->validateData($requestData, $this->rules)) {
            return ResponseHelper::validationError(lang('Validation.failed'), $this->validator->getErrors());
        };

        $validData = $this->validator->getValidated();

        $coaster = $this->coastersService->get(Uuid::fromString($uuid));
        if(!$coaster) {
            return ResponseHelper::notFound('Coaster', $validData['uuid']);
        }

        $updated = $this->coastersService->update($coaster, UpdateCoasterData::fromArray($validData));

        return ResponseHelper::updated('Coaster', $updated->toArray());
    }
}
