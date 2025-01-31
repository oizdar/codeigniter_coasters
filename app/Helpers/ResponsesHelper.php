<?php

namespace App\Helpers;

use CodeIgniter\HTTP\ResponseInterface;

class ResponsesHelper
{

    public static function prepare(?string $message, int $statusCode, ?array $data = null, ): ResponseInterface
    {
        return response()->setJSON([
            'message' => $message,
            'data' => $data,
        ])->setStatusCode($statusCode);
    }

    public static function created(string $modelName, $data = null): ResponseInterface
    {
        return self::prepare(
            message: lang('Response.created', ['model' => $modelName]),
            statusCode: ResponseInterface::HTTP_CREATED,
            data: $data
        );
    }

    public static function updated(string $modelName, $data = null): ResponseInterface
    {
        return self::prepare(
            message: lang('Response.updated', ['model' => $modelName]),
            statusCode: ResponseInterface::HTTP_OK,
            data: $data
        );
    }


    public static function success(?string $message, $data = null): ResponseInterface
    {
        return self::prepare(
            message: $message,
            statusCode: ResponseInterface::HTTP_OK,
            data: $data
        );
    }

    public static function error(?string $message, $data = null): ResponseInterface
    {
        return self::prepare(
            message: $message,
            statusCode: ResponseInterface::HTTP_BAD_REQUEST,
            data: $data
        );
    }

    public static function notFound(string $modelName, string $uuid): ResponseInterface
    {
        return self::prepare(
            message: lang('Response.not_found', ['model' => $modelName, 'uuid' => $uuid]),
            statusCode: ResponseInterface::HTTP_NOT_FOUND,
        );
    }
}