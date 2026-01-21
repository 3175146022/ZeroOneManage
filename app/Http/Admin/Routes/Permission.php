<?php

declare(strict_types=1);

use App\Http\Admin\Controller\Permission\AdminController;
use App\Http\Admin\Controller\Permission\AdminOpLogController;
use App\Http\Admin\Controller\Permission\GroupController;
use App\Http\Admin\Controller\Permission\RuleController;
use App\Http\Admin\Middleware\PermissionMiddleware;
use Hyperf\HttpServer\Router\Router;

// 权限管理
Router::addGroup('/permission', function () {
    // 菜单
    Router::addGroup('/group', function () {
        Router::get('/index', [GroupController::class, 'index'], ['desc' => '菜单列表查询']);
        Router::get('/detail', [GroupController::class, 'detail'], ['desc' => '菜单详情查询']);
        Router::post('/create', [GroupController::class, 'create'], ['desc' => '菜单创建']);
        Router::post('/update', [GroupController::class, 'update'], ['desc' => '菜单更新']);
        Router::post('/delete', [GroupController::class, 'delete'], ['desc' => '菜单删除']);
        Router::get('/option', [GroupController::class, 'option']);
        Router::get('/menuIds', [GroupController::class, 'getGroupMenuIds']);
    });

    // 规则
    Router::addGroup('/rule', function () {
        Router::get('/index', [RuleController::class, 'index'], ['desc' => '菜单规则列表查询']);
        Router::get('/detail', [RuleController::class, 'detail'], ['desc' => '菜单规则详情查询']);
        Router::post('/create', [RuleController::class, 'create'], ['desc' => '菜单规则创建']);
        Router::post('/update', [RuleController::class, 'update'], ['desc' => '菜单规则更新']);
        Router::post('/delete', [RuleController::class, 'delete'], ['desc' => '菜单规则删除']);
        Router::get('/option', [RuleController::class, 'option']);
    });

    // 管理员
    Router::addGroup('/admin', function () {
        Router::get('/index', [AdminController::class, 'index'], ['desc' => '管理员列表查询']);
        Router::get('/detail', [AdminController::class, 'detail'], ['desc' => '管理员详情查询']);
        Router::post('/create', [AdminController::class, 'create'], ['desc' => '管理员创建']);
        Router::post('/update', [AdminController::class, 'update'], ['desc' => '管理员更新']);
        Router::post('/delete', [AdminController::class, 'delete'], ['desc' => '管理员删除']);
    });

    // 日志
    Router::addGroup('/oplog', function () {
        Router::get('/index', [AdminOpLogController::class, 'index'], ['desc' => '管理员操作日志列表查询']);
        Router::get('/detail', [AdminOpLogController::class, 'detail']);
    });
}, ['middleware' => [PermissionMiddleware::class => 99]]);