<?php

declare(strict_types=1);

namespace App\Exception;

use App\Http\Common\Enums\Code;
use App\Http\Common\Result;
use RuntimeException;

class BusinessException extends RuntimeException
{
    private Result $response;

    public function __construct(Code $code = Code::FAIL, ?string $message = null, mixed $data = [])
    {
        $this->response = new Result($code, $message, $data);

        // 调用父类构造函数
        parent::__construct(
            message: $this->response->message ?: '业务异常',
            code: $this->response->code->value,
        );
    }

    public function getResponse(): Result
    {
        return $this->response;
    }
}
