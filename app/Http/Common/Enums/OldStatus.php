<?php
declare(strict_types=1);

namespace App\Http\Common\Enums;

use Hyperf\Constants\Annotation\Message;
use Hyperf\Constants\EnumConstantsTrait;

/**
 * 状态枚举(旧系统状态)
 */
enum OldStatus: string
{
    use EnumConstantsTrait;

    /**
     * 正常
     */
    #[Message('status.normal')]
    case Normal = 'normal';

    /**
     * 隐藏
     */
    #[Message('status.hidden')]
    case Hidden = 'hidden';

    /**
     * 禁用
     */
    #[Message('status.disable')]
    case Disable = 'disable';

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
