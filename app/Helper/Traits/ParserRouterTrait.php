<?php

namespace App\Helper\Traits;

use function count;
use function is_array;
use function is_string;

/**
 * 解析路由器特性
 */
trait ParserRouterTrait
{
    final protected function parse(mixed $callback): ?array
    {
        if (is_array($callback) && count($callback) === 2) {
            return $callback;
        }
        if (is_string($callback)) {
            if (str_contains($callback, '@')) {
                $exp = explode('@', $callback);
            }
            if (str_contains($callback, '::')) {
                $exp = explode('::', $callback);
            }
            if (isset($exp) && count($exp) === 2) {
                return $exp;
            }
        }
        return null;
    }
}