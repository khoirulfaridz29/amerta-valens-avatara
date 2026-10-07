<?php

use App\Http\Middleware\AjaxFlashToJson;
use App\Http\Middleware\EnsureRole;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => EnsureRole::class,
        ]);
        $middleware->web(append: [
            SecurityHeaders::class,
            AjaxFlashToJson::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();

// Di shared hosting (mis. InfinityFree) web root bisa berbeda dari {base}/public.
// Set APP_PUBLIC_PATH ke folder web root (htdocs) agar upload & asset tampil.
if ($publicPath = env('APP_PUBLIC_PATH')) {
    $app->usePublicPath($publicPath);
}

return $app;
