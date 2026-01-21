<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Http\Common\Enums\Code;
use App\Http\Common\Result;
use Hyperf\Database\Model\ModelNotFoundException;
use Throwable;

final class ModeNotFoundHandler extends AbstractHandler
{
    /**
     * @param Throwable $throwable
     * @return Result
     */
    public function handleResponse(Throwable $throwable): Result
    {
        $this->stopPropagation();
        return new Result(
            code: Code::NOT_FOUND,
        );
    }

    /**
     * @param Throwable $throwable
     * @return bool
     */
    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ModelNotFoundException;
    }
}
