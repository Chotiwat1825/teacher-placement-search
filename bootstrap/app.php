<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__ . '/../routes/web.php', commands: __DIR__ . '/../routes/console.php', health: '/up')
    ->withMiddleware(function (Middleware $middleware) {
        // Laravel จะเพิ่ม default middleware ที่จำเป็นให้โดยอัตโนมัติ
        // เช่น EncryptCookies, StartSession, VerifyCsrfToken ฯลฯ สำหรับ 'web' group
        // และ middleware สำหรับ 'auth' และ 'guest' ก็ควรจะถูกจัดการโดย framework
        $middleware->web(
            append: [
                // หรือ $middleware->appendToGroup('web', [
                \App\Http\Middleware\UpdateLastSeenAt::class,
            ],
        );
        // คุณเพียงแค่ต้องเพิ่ม custom aliases ของคุณ
        $middleware->alias([
            'admin' => \App\Http\Middleware\IsAdminMiddleware::class,
            // 'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class, // ถ้าต้องการใช้ Email Verification
        ]);

        // ถ้าต้องการ custom middleware groups หรือ global middleware ก็เพิ่มที่นี่
        // $middleware->group('web', [
        //    \App\Http\Middleware\AnotherWebMiddleware::class,
        // ]);
        // $middleware->append(MyGlobalMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
