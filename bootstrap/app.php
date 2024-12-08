<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'check_permission' => \App\Http\Middleware\CheckPermission::class,
            'check_user_permission' => \App\Http\Middleware\CheckUserProfilePermission::class,
            'check_team_permission' => \App\Http\Middleware\CheckTeamPermission::class,
            'check_user_password' => \App\Http\Middleware\CheckUserPassword::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
