<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class AbstractApiController extends Controller
{
    protected const STATUS_CODE_200 = 200;
    protected const STATUS_CODE_404 = 404;
    protected const STATUS_CODE_422 = 422;

    /**
     * @param array $data
     * @param int|string $httpCode
     * @param int|string $statusCode
     * @param bool $camelCase
     * @param array $errors
     * @return JsonResponse
     */
    public function response(
        array      $data = [],
        int|string $httpCode = self::STATUS_CODE_200,
        int|string $statusCode = self::STATUS_CODE_200,
        bool       $camelCase = true,
        array      $errors = [],
    ): JsonResponse {
        $data = $camelCase ? snakeToCamel($data) : $data;

        return response()->json([
            'data' => $data,
            'status' => $statusCode === self::STATUS_CODE_200,
            'errors' => $errors
        ], $httpCode);
    }
}
