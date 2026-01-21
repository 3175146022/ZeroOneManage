<?php
declare(strict_types=1);

namespace App\Http\Common\Middleware;

use App\Helper\Processor\UuidRequestIdProcessor;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 请求ID中间件
 */
final class RequestIdMiddleware implements MiddlewareInterface
{
    /**
     * 处理请求
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request)->setHeader('Request-Id', UuidRequestIdProcessor::getUuid());
    }
}