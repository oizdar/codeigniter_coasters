<?php

namespace App\Controllers\Coasters;

use App\Controllers\BaseController;
use App\Helpers\ResponseHelper;
use App\Libraries\Coasters\CoastersService;
use App\Libraries\Coasters\CreateCoasterData;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class CreateCoaster extends BaseController
{
    private array $rules = [
        'number_of_staff' => [
            'required',
            'integer',
            'greater_than_equal_to[1]',
            'less_than_equal_to[100]',
        ],
        'number_of_clients' => [
            'required',
            'integer',
            'greater_than_equal_to[1]',
            'less_than_equal_to[1000000]',

        ],
        'route_length' => [
            'required',
            'integer',
            'greater_than_equal_to[1]',
            'less_than_equal_to[100000]',
        ],
        'hours_from' => [
            'required',
            'valid_time',
        ],
        'hours_to' => [
            'required',
            'valid_time',
        ],
    ];

    private CoastersService $coastersService;

    public function __construct()
    {
        $this->coastersService = Services::coastersService();
    }

    public function __invoke(): ResponseInterface
    {
        $requestData = $this->request->getJSON(true);
        if(!$this->validateData($requestData, $this->rules)) {
            return ResponseHelper::validationError(lang('Validation.failed'), $this->validator->getErrors());
        };

        $validData = $this->validator->getValidated();

        $model = $this->coastersService->create(CreateCoasterData::fromArray($validData));


        return ResponseHelper::created('Coaster', $model->toArray());
    }
}
