<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Http\Common\Enums\Code;
use App\Http\Common\Result;
use Hyperf\Validation\ValidationException;
use Throwable;

final class ValidationExceptionHandler extends AbstractHandler
{
    /**
     * @param ValidationException $throwable
     */
    public function handleResponse(Throwable $throwable): Result
    {
        $this->stopPropagation();
        return new Result(
            code: Code::UNPROCESSABLE_ENTITY,
            message: $throwable->validator->errors()->first()
        );
    }

    /**
     * @param Throwable $throwable
     * @return bool
     */
    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ValidationException;
    }
}
