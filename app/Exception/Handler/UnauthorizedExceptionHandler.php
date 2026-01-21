<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Http\Common\Enums\Code;
use App\Http\Common\Result;
use Hyperf\Validation\UnauthorizedException;
use Throwable;

/**
 * 未授权异常捕获
 */
final class UnauthorizedExceptionHandler extends AbstractHandler
{
    public function handleResponse(Throwable $throwable): Result
    {
        $this->stopPropagation();
        return new Result(
            Code::FORBIDDEN,
        );
    }

    /**
     * @param Throwable $throwable
     * @return bool
     */
    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof UnauthorizedException;
    }
}
