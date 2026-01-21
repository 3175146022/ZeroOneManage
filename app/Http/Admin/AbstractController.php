<?php
declare(strict_types=1);

namespace App\Http\Admin;

use App\Http\Common\Controller\AbstractController as Base;
use Hyperf\Context\ApplicationContext;
use Hyperf\HttpServer\Contract\RequestInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 后台抽象控制器
 */
class AbstractController extends Base
{
    /**
     * 获取当前页码
     *
     * @return int
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getCurrentPage(): int
    {
        return (int)$this->getRequest()->input('page', 1);
    }

    /**
     * 获取每页数量
     *
     * @return int
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getPageSize(): int
    {
        return (int)$this->getRequest()->input('per_page', 10);
    }

    /**
     * 获取请求数据
     *
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getRequestData(): array
    {
        return $this->getRequest()->all();
    }

    /**
     * 获取请求实例
     *
     * @return RequestInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getRequest(): RequestInterface
    {
        return ApplicationContext::getContainer()->get(RequestInterface::class);
    }

    /**
     * 获取用户令牌
     *
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getUserToken(): string
    {
        return $this->getRequest()->getHeaderLine('X-Access-Token');
    }
}