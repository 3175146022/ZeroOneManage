<?php
declare(strict_types=1);

namespace App\Http\Common\Enums;

use Hyperf\Constants\Annotation\Constants;
use Hyperf\Constants\Annotation\Message;
use Hyperf\Constants\ConstantsTrait;

/**
 * 统一状态码枚举
 */
#[Constants]
enum Code: int
{
    use ConstantsTrait;

    /**
     * 成功
     */
    #[Message('code.success')]
    case SUCCESS = 200;

    /**
     * 失败
     */
    #[Message('code.fail')]
    case FAIL = 0;

    /**
     * 服务错误
     */
    #[Message('code.serviceError')]
    case SERVICE_ERROR = 500;

    /**
     * 未授权
     */
    #[Message('code.unauthorized')]
    case UNAUTHORIZED = 401;

    /**
     * 禁止访问
     */
    #[Message('code.forbidden')]
    case FORBIDDEN = 403;

    /**
     * 资源未找到
     */
    #[Message('code.notFound')]
    case NOT_FOUND = 404;

    /**
     * 请求参数错误
     */
    #[Message('result.conflict')]
    case UNPROCESSABLE_ENTITY = 422;
}