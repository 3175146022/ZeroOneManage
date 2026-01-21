<?php

declare(strict_types=1);

use App\Http\Common\Middleware\RequestIdMiddleware;
use App\Http\Common\Middleware\TranslationMiddleware;
use Hyperf\Validation\Middleware\ValidationMiddleware;

return [
    'http' => [
        // 请求ID中间件
        RequestIdMiddleware::class,
        // 多语言识别中间件
        TranslationMiddleware::class,
        // 验证器中间件,处理 formRequest 验证器
        ValidationMiddleware::class,
    ],
];
