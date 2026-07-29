<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
//use Throwable;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->render(function (ValidationException $e, $request) {

            if (!$request->expectsJson()) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'data' => null,
                'errors' => $e->errors(),
                'meta' => null,
            ], 422);

        });

        $exceptions->render(function (AuthenticationException $e, $request) {

            if (!$request->expectsJson()) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'data' => null,
                'errors' => null,
                'meta' => null,
            ], 401);

        });


        $exceptions->render(function (AuthorizationException $e, $request) {

            if (!$request->expectsJson()) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'Forbidden.',
                'data' => null,
                'errors' => null,
                'meta' => null,
            ], 403);

        });

        $exceptions->render(function (ModelNotFoundException $e, $request) {

            if (!$request->expectsJson()) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'Resource not found.',
                'data' => null,
                'errors' => null,
                'meta' => null,
            ], 404);

        });

        $exceptions->render(function (NotFoundHttpException $e, $request) {

            if (!$request->expectsJson()) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'Route not found.',
                'data' => null,
                'errors' => null,
                'meta' => null,
            ], 404);

        });

        $exceptions->render(function (Throwable $e, $request) {

            if (!$request->expectsJson()) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => config('app.debug')
                    ? $e->getMessage()
                    : 'Something went wrong.',
                'data' => null,
                'errors' => null,
                'meta' => null,
            ], 500);

        });

        //
    })->create();
