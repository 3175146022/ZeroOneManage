<?php
declare(strict_types=1);

namespace App\Http\Common\Middleware;

use Hyperf\Contract\TranslatorInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 多语言识别中间件
 */
final class TranslationMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly TranslatorInterface $translator) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->translator->setLocale($this->getLocale($request));
        return $handler->handle($request);
    }

    /**
     * 获取语言标识
     *
     * @param ServerRequestInterface $request
     * @return string
     */
    protected function getLocale(ServerRequestInterface $request): string
    {
        $locale = null;
        if ($request->hasHeader('Accept-Language')) {
            $locale = $request->getHeaderLine('Accept-Language');
        }
        return $locale ?: 'zh_CN';
    }
}