<?php

declare(strict_types=1);

namespace App\Helper\Attribute;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;
use function is_array;

/**
 * 权限注解
 *
 * @Annotation
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final class Permission extends AbstractAnnotation
{
    public const OPERATION_AND = 'and';

    public const OPERATION_OR = 'or';

    public function __construct(
        protected array|string $code,
        protected string       $operation = self::OPERATION_AND,
    ) {}

    public function getCode(): array
    {
        return is_array($this->code) ? $this->code : [$this->code];
    }

    public function getOperation(): string
    {
        return $this->operation;
    }
}
