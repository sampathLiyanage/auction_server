<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ModelNotFoundException || $exception instanceof NotFoundHttpException) {
            return response()->json([
                'error' => 'Resource not found'
            ], 404);
        } else if ($exception instanceof BadRequestException) {
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);
        } else if ($exception instanceof UnauthorizedException) {
            return response()->json([
                'error' => $exception->getMessage()
            ], 401);
        } else if ($exception instanceof \Exception) {
            return response()->json([
                'error' => 'Internal Server Error'
            ], 500);
        }

        return parent::render($request, $exception);
    }
}
