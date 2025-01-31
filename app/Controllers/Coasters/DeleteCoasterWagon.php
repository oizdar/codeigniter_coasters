<?php

namespace App\Controllers\Coasters;

use App\Controllers\BaseController;
use App\Helpers\ResponseHelper;
use App\Libraries\Coasters\CoastersService;
use App\Libraries\Coasters\CreateCoasterWagonData;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Ramsey\Uuid\Uuid;

class DeleteCoasterWagon extends BaseController
{
    private $rules = [
        'coaster_uuid' => [
            'required',
            'uuid',
        ],
        'wagon_uuid' => [
            'required',
            'uuid',
        ],
    ];

    private CoastersService $coastersService;

    public function __construct()
    {
        $this->coastersService = Services::coastersService();
    }

    public function __invoke(string $coasterUuid, string $wagonUuid): ResponseInterface
    {
        $requestData['coaster_uuid'] = $coasterUuid;
        $requestData['wagon_uuid'] = $wagonUuid;

        if(!$this->validateData($requestData, $this->rules)) {
            return ResponseHelper::validationError(lang('Validation.failed'), $this->validator->getErrors());
        };

        $validData = $this->validator->getValidated();

        $coaster = $this->coastersService->get(Uuid::fromString($validData['coaster_uuid']));
        if(!$coaster) {
            return ResponseHelper::notFound('Coaster', $validData['coaster_uuid']);
        }

        $wagon = $coaster->getWagonByUuid(Uuid::fromString($validData['wagon_uuid']));

        if(!$wagon) {
            return ResponseHelper::notFound('Wagon', $validData['wagon_uuid']);
        }

        $this->coastersService->deleteWagon($coaster, $wagon->uuid);

        return ResponseHelper::noContent();
    }
}
