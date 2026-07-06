<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/physical-possession.php'));
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api-possession.php'));

            Route::middleware('web')
                ->group(base_path('routes/mmgayAuth.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'guest' => \Illuminate\Auth\Middleware\RedirectIfAuthenticated::class,
            'district.officer' => \App\Http\Middleware\DistrictOfficerMiddleware::class,
            'mmgay' => \App\Http\Middleware\MMGAYMiddleware::class,
        ]);



    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The requested application secure ID was not found or does not exist.'
                ], 404);
            }
        });
    })->create();