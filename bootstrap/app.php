<?php

use App\Helpers\ApiResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Auth\AuthenticationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                
                if ($e instanceof ValidationException) {
                    return ApiResponse::error('Validation Error', 422, $e->errors());
                }

                if ($e instanceof AuthenticationException) {
                    return ApiResponse::error('Unauthenticated. Please login first.', 401);
                }

                if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
                    return ApiResponse::error('Resource not found.', 404);
                }

                if ($e instanceof AccessDeniedHttpException) {
                    return ApiResponse::error('Unauthorized access.', 403);
                }

                if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                    return ApiResponse::error($e->getMessage() ?: 'HTTP Error', $e->getStatusCode());
                }

                return ApiResponse::error(
                    config('app.debug') ? $e->getMessage() : 'Something went wrong. Please try again later.',
                    500
                );
            }
        });
    })->create();