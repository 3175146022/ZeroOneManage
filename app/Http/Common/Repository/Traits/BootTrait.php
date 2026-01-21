<?php

declare(strict_types=1);

namespace App\Http\Common\Repository\Traits;

use function Hyperf\Support\class_basename;
use function Hyperf\Support\class_uses_recursive;

/**
 * 自动加载 Trait
 *
 * @package App\Http\Common\Repository\Traits
 */
trait BootTrait
{
    protected function startBoot(...$params): void
    {
        $traits = class_uses_recursive(static::class);
        foreach ($traits as $trait) {
            $method = 'boot' . class_basename($trait);
            if (method_exists($this, $method)) {
                $this->{$method}(...$params);
            }
        }
    }
}
