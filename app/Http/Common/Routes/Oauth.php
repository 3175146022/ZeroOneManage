<?php

declare(strict_types=1);

use Hyperf\HttpServer\Router\Router;

// 获取认证凭证
Router::get('/certificate', 'App\Http\Common\Controller\OauthController@certificate');