<?php
declare(strict_types=1);

namespace App\Http\Common\Controller;

use App\Http\Common\Enums\Code;
use App\Http\Common\Result;

/**
 * 统一抽象控制器基类
 */
abstract class AbstractController
{
    /**
     * 成功
     *
     * @param mixed       $data
     * @param string|null $message
     * @return Result
     */
    protected function success(mixed $data = [], ?string $message = null): Result
    {
        return new Result(Code::SUCCESS, $message, $data);
    }

    /**
     * 错误
     *
     * @param mixed       $data
     * @param string|null $message
     * @return Result
     */
    protected function error(mixed $data = [], ?string $message = null): Result
    {
        return new Result(Code::FAIL, $message, $data);
    }

    /**
     * 自定义响应
     *
     * @param Code        $code
     * @param mixed       $data
     * @param string|null $message
     * @return Result
     */
    protected function json(Code $code, mixed $data = [], ?string $message = null): Result
    {
        return new Result($code, $message, $data);
    }
}