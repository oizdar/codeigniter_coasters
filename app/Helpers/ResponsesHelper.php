<?php

namespace App\Helpers;

use CodeIgniter\HTTP\ResponseInterface;

class ResponsesHelper
{

    public static function create(?string $message, int $statusCode, ?array $data = null, )
    {
        return response()->setJSON([
            'message' => $message,
            'data' => $data,
        ])->setStatusCode($statusCode);
    }

    public static function created($modelName, $data = null)
    {
        return self::create(
            message: lang('Response.created', ['model' => $modelName]),
            statusCode: ResponseInterface::HTTP_CREATED,
            data: $data
        );
    }

    public static function success(?string $message, $data = null)
    {
        return self::create(
            message: $message,
            statusCode: ResponseInterface::HTTP_OK,
            data: $data
        );
    }

    public static function error(?string $message, $data = null)
    {
        return self::create(
            message: $message,
            statusCode: ResponseInterface::HTTP_BAD_REQUEST,
            data: $data
        );
    }
}