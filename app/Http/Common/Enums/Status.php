<?php
declare(strict_types=1);

namespace App\Http\Common\Enums;

use Hyperf\Constants\Annotation\Message;
use Hyperf\Constants\EnumConstantsTrait;

/**
 * 状态枚举
 */
enum Status: int
{
    use EnumConstantsTrait;

    /**
     * 正常
     */
    #[Message('status.normal')]
    case Normal = 1;

    /**
     * 隐藏
     */
    #[Message('status.hidden')]
    case Hidden = 2;

    /**
     * 禁用
     */
    #[Message('status.disable')]
    case Disable = 3;

    public function isNormal(): bool
    {
        return $this === self::Normal;
    }

    public function isDisable(): bool
    {
        return $this === self::Disable;
    }

    public function isHidden(): bool
    {
        return $this === self::Hidden;
    }
}
