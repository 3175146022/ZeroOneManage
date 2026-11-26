<?php

declare(strict_types=1);

use function Hyperf\Support\env;

return [
    // 使用应用本身的 HTTP 路由发布文档，关闭扩展自带的独立监听端口。
    'enable'          => false,
    'serve_documents' => env('SWAGGER_ENABLED', true),
    'json_dir'        => BASE_PATH . '/storage/swagger',
    'scan'            => [
        'paths' => [BASE_PATH . '/app/Http'],
    ],
    'server'          => [
        'http' => [
            'servers' => [
                [
                    'url'         => env('SWAGGER_API_URL', '/'),
                    'description' => 'HTTP API',
                ]
            ],
        ],
    ],
];
