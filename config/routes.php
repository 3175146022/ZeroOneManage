<?php

declare(strict_types=1);

use App\Http\Admin\Middleware\OperationMiddleware;
use App\Http\Common\Controller\SwaggerController;
use App\Http\Common\Middleware\SignatureMiddleware;
use Hyperf\HttpServer\Router\Router;
use function Hyperf\Support\env;

/**
 * 路由工厂类，用于动态加载路由文件。
 */
final class RouteFactory
{
    /**
     * 创建路由组
     *
     * @param string $prefix     路由前缀
     * @param string $path       路由文件路径
     * @param array  $middleware 中间件列表
     * @return void
     */
    public static function create(string $prefix, string $path, array $middleware = []): void
    {
        Router::addGroup(
            $prefix,
            fn() => self::loadRouteFiles($prefix, $path),
            ['middleware' => $middleware]
        );
    }

    /**
     * 批量创建路由组
     *
     * @param array $routes 路由配置数组
     * @return void
     */
    public static function createMultiple(array $routes): void
    {
        foreach ($routes as $route) {
            self::create(
                $route['prefix'] ?? '',
                $route['path'] ?? '',
                $route['middleware'] ?? []
            );
        }
    }

    /**
     * 加载路由文件
     *
     * @param string $prefix 路由前缀
     * @param string $path   路由文件路径
     * @return void
     */
    private static function loadRouteFiles(string $prefix, string $path): void
    {
        if (!is_dir($path)) {
            throw new RuntimeException("Routes directory not found: $path");
        }

        $files = glob($path . '*.php') ?: [];
        sort($files);

        $loaded = [];
        foreach ($files as $file) {
            if (is_file($file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                require_once $file;
                $loaded[] = basename($file);
            }
        }

        if (env('APP_ENV') === 'dev' && !empty($loaded)) {
            // TODO : 使用更合适的日志记录方式
            error_log("$prefix routes loaded: " . implode(', ', $loaded));
        }
    }
}

// 使用 RouteFactory 加载路由
RouteFactory::createMultiple([
    // 管理端路由
    [
        'prefix'     => '/admin',
        'path'       => BASE_PATH . '/app/Http/Admin/Routes/',
        'middleware' => [
            SignatureMiddleware::class => 100,
            OperationMiddleware::class => 98,
        ],
    ],
    // 认证路由
    [
        'prefix'     => '/oauth',
        'path'       => BASE_PATH . '/app/Http/Common/Routes/',
        'middleware' => []
    ]
]);

if (env('SWAGGER_ENABLED', true)) {
    Router::get('/swagger', [SwaggerController::class, 'index']);
    Router::get('/swagger/openapi.json', [SwaggerController::class, 'specification']);
}
