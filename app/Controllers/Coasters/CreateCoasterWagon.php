<?php

namespace App\Controllers\Coasters;

use App\Controllers\BaseController;
use App\Helpers\ResponsesHelper;
use App\Libraries\Coasters\CoastersService;
use App\Libraries\Coasters\CreateCoasterData;
use App\Libraries\Coasters\CreateCoasterWagonData;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Ramsey\Uuid\Uuid;

class CreateCoasterWagon extends BaseController
{
    private $rules = [
        'coaster_uuid' => [
            'required',
            'uuid',
        ],
        'number_of_places' => [
            'required',
            'integer',
            'greater_than_equal_to[1]',
            'less_than_equal_to[100]',
        ],
        'speed' => [
            'required',
            'numeric',
            'greater_than[0]',
            'less_than_equal_to[100]',
        ],
    ];

    private CoastersService $coastersService;

    public function __construct()
    {
        $this->coastersService = Services::coastersService();
    }

    public function __invoke(string $coasterUuid): ResponseInterface
    {
        $requestData = $this->request->getJSON(true);
        $requestData['coaster_uuid'] = $coasterUuid;

        if(!$this->validateData($requestData, $this->rules)) {
            return ResponsesHelper::error(lang('Validation.failed'), $this->validator->getErrors());
        };

        $validData = $this->validator->getValidated();

        $coaster = $this->coastersService->get(Uuid::fromString($coasterUuid));
        if(!$coaster) {
            return ResponsesHelper::notFound('Coaster', $coasterUuid);
        }

        if(!$this->validateData($requestData, $this->rules)) {
            return ResponsesHelper::error(lang('Validation.failed'), $this->validator->getErrors());
        }

        $validData = $this->validator->getValidated();

        $wagon = $this->coastersService->addWagon($coaster, CreateCoasterWagonData::fromArray($validData));

        return ResponsesHelper::created('Wagon', $wagon->toArray());
    }
}
