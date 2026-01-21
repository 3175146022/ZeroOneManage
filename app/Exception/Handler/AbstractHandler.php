<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Helper\Processor\UuidRequestIdProcessor;
use App\Helper\Traits\Debugging;
use App\Http\Common\Result;
use Closure;
use Hyperf\Codec\Json;
use Hyperf\Context\Context;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\ExceptionHandler\Formatter\FormatterInterface;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Swow\Psr7\Message\ResponsePlusInterface;
use Throwable;

/**
 * 抽象异常处理器
 *
 * @package App\Exception\Handler
 */
abstract class AbstractHandler extends ExceptionHandler
{
    use Debugging;

    private const CONTEXT_KEY = __CLASS__ . '.throwable';

    public function __construct(
        private readonly ConfigInterface    $config,
        private readonly ContainerInterface $container,
        private readonly LoggerFactory      $loggerFactory
    ) {}

    abstract public function handleResponse(Throwable $throwable): Result;

    /**
     * 处理异常
     *
     * @param Throwable             $throwable
     * @param ResponsePlusInterface $response
     * @return Closure
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(Throwable $throwable, ResponsePlusInterface $response)
    {
        $this->report($throwable);

        // 如果是 debug 模式，记录异常信息
        if ($this->isDebug()) {
            Context::set(self::CONTEXT_KEY, [
                'message' => $throwable->getMessage(),
                'file' => $throwable->getFile(),
                'line' => $throwable->getLine(),
                'trace' => $throwable->getTrace(),
            ]);
        }

        return value(function (ResponsePlusInterface $responsePlus) use ($throwable) {
            // 如果是 debug 模式，自动处理跨域
            if ($this->isDebug()) {
                $responsePlus
                    ->setHeader('Access-Control-Allow-Origin', '*')
                    ->setHeader('Access-Control-Allow-Credentials', 'true')
                    ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, DELETE, OPTIONS')
                    ->setHeader('Access-Control-Allow-Headers', 'DNT,Keep-Alive,User-Agent,Cache-Control,Content-Type,Authorization');
            }
            return $responsePlus;
        }, $this->handlerRequestId(
            $this->handlerResult(
                $response,
                $this->handleResponse($throwable)
            )
        ));
    }

    /**
     * 上报日志 + 打印错误.
     *
     * @param Throwable $throwable
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function report(Throwable $throwable): void
    {
        // 如果是debug模式，打印错误到控制台
        if ($this->isDebug()) {
            $this->container->get(StdoutLoggerInterface::class)->error(
                $this->container->get(FormatterInterface::class)->format($throwable)
            );
        }
        $this->loggerFactory
            ->get('error')
            ->error($throwable->getMessage(), ['exception' => $throwable]);
    }

    /**
     * 处理result 打包到 response body 中.
     *
     * @param ResponsePlusInterface $responsePlus
     * @param Result                $result
     * @return ResponsePlusInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function handlerResult(ResponsePlusInterface $responsePlus, Result $result): ResponsePlusInterface
    {
        $responsePlus->setHeader('Content-Type', 'application/json; charset=utf-8');

        if ($this->isDebug()) {
            $result = $result->toArray();
            $result['throwable'] = Context::get(self::CONTEXT_KEY);
            return $responsePlus->setBody(new SwooleStream(Json::encode($result)));
        }

        return $responsePlus->setBody(new SwooleStream(Json::encode($result)));
    }

    /**
     * 处理 response 加上 request-id 信息.
     *
     * @param ResponsePlusInterface $responsePlus
     * @return ResponsePlusInterface
     */
    private function handlerRequestId(ResponsePlusInterface $responsePlus): ResponsePlusInterface
    {
        return $responsePlus->setHeader('Request-Id', UuidRequestIdProcessor::getUuid());
    }
}
