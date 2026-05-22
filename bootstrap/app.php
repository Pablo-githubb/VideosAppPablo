<?php

use App\Http\Middleware\SetTeamUrlDefaults;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SetTeamUrlDefaults::class,
        ]);

        if (class_exists(\PHPUnit\Framework\TestCase::class) || (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'testing')) {
            $middleware->validateCsrfTokens(except: ['*']);
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
