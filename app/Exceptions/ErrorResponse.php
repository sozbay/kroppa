<?php

namespace App\Exceptions;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Response;

class ErrorResponse
{

    static function errorResponse($errorMessage, $status = 400): Application|Response|\Illuminate\Contracts\Foundation\Application|ResponseFactory
    {
        $obj = [
            'success' => false,
            'errorMessage' => $errorMessage
        ];
        return response($obj, $status);
    }
}
