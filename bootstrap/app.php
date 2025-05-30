<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware; // Pastikan ini di-import

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Daftarkan alias middleware Anda di sini
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            // Anda juga akan melihat alias lain yang mungkin sudah ada atau ditambahkan oleh package lain
            // 'auth' => \App\Http\Middleware\Authenticate::class, // Ini biasanya sudah dihandle secara otomatis atau berbeda
            // 'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class, // Sama seperti auth
        ]);

        // Anda juga bisa menambahkan middleware global atau grup di sini jika perlu
        // $middleware->web(append: [
        //     \App\Http\Middleware\ExampleMiddleware::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // ...
    })->create();