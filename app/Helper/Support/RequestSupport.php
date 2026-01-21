<?php

declare(strict_types=1);

namespace App\Helper\Support;

use App\Http\Common\Request\ClientIpTrait;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Request;

/**
 * 请求支持
 */
class RequestSupport extends Request implements RequestInterface
{
    use ClientIpTrait;
}
