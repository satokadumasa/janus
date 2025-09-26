<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use App\Http\Middleware\CustomSessionCookie;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            EnsureFrontendRequestsAreStateful::class,
        ]);
        $middleware->api([
            EnsureFrontendRequestsAreStateful::class,
        ]);
        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
            'auth:sanctum' => EnsureFrontendRequestsAreStateful::class,
        ]);
        $middleware->statefulApi();
        $middleware->append(CustomSessionCookie::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
    })->create();
