<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Daftarkan middleware Anda di sini

        // Contoh pendaftaran alias route middleware
        // Ini setara dengan bagian $routeMiddleware di Kernel.php pada versi Laravel sebelumnya.
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class, // <--- TAMBAHKAN BARIS INI
        ]);

        // Jika Anda memiliki middleware global (yang berjalan di setiap permintaan),
        // Anda akan menambahkannya di sini:
        // $middleware->web(append: [
        //     // \App\Http\Middleware\TrustProxies::class,
        // ]);

        // Atau untuk API middleware:
        // $middleware->api(prepend: [
        //     // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();