<?php

use App\Http\Middleware\Admin;
use App\Http\Middleware\AdminPermission;
use App\Http\Middleware\notAdmin;
use App\Http\Middleware\Pending;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => Admin::class,
            'admin.permission' => AdminPermission::class,
            'not_admin' => notAdmin::class,
            'pending' => Pending::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
