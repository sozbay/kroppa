<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if ($request->wantsJson()) {
            return $this->handleApiException($request, $e);
        } else {
            $retval = parent::render($request, $e);
        }
        return $retval;
    }

    private function handleApiException($request, $exception)
    {
        $exception = $this->prepareException($exception);

        if ($exception instanceof HttpResponseException) {
            $exception = $exception->getResponse();
        }

        if ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        }

        if ($exception instanceof ValidationException) {
            $messages = $exception->validator->getMessageBag()->getMessages();
            foreach ($messages as $k => $message) {
                $errors[] = [
                    'label' => $k,
                    'msg' => value($message)
                ];
            }
            return ErrorResponse::errorResponse($errors ?? []);
        };

        if ($exception instanceof NotFoundHttpException) {
            return ErrorResponse::errorResponse($exception->getMessage(), 404);
        }

        return ErrorResponse::errorResponse($exception->getMessage(), $exception->getCode() ?? 404);

    }
}
