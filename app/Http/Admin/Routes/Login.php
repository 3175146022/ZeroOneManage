<?php

declare(strict_types=1);

use App\Http\Admin\Controller\LoginController;
use App\Http\Admin\Controller\Permission\AdminController;
use App\Http\Admin\Middleware\PermissionMiddleware;
use Hyperf\HttpServer\Router\Router;

// 登录相关路由
Router::addGroup('/login', function () {
    // 获取登录图形验证码
    Router::get('/captcha', [LoginController::class, 'captcha']);
    // 登录
    Router::post('', [LoginController::class, 'login']);
});

// 当前登录用户相关路由
Router::addGroup('/current_user', function () {
    // 获取用户信息
    Router::get('/info', [AdminController::class, 'getCurrentUser']);
    // 获取用户权限
    Router::get('/permissions', [AdminController::class, 'getCurrentUserRoutes']);
    // 刷新token
    Router::post('/refresh_token', [LoginController::class, 'refresh']);
    // 登出
    Router::post('/logout', [LoginController::class, 'logout']);
}, ['middleware' => [PermissionMiddleware::class]]);