<?php
declare(strict_types=1);

namespace App\Http\Common\Enums;

use App\Exception\BusinessException;
use function Hyperf\Config\config;
use function Hyperf\Translation\__;

/**
 * 缓存键名枚举基类
 */
abstract class Cache
{
    /**
     * 获取完整的 Redis 键名
     *
     * @param string|int ...$params
     * @return string
     */
    public function getKey(string|int ...$params): string
    {
        $value = config('cache.default.prefix') . $this->getValue();
        $expectedCount = substr_count($value, '%');
        $actualCount = count(array_filter($params));

        if ($expectedCount !== $actualCount) {
            throw $this->createParamException($expectedCount, $actualCount);
        }

        return sprintf($value, ...$params);
    }

    /**
     * 获取所有枚举实例
     *
     * @return array
     */
    public static function cases(): array
    {
        return static::getAllValues();
    }

    /**
     * 通过名称获取枚举实例
     *
     * @param string $name
     * @return static
     */
    public static function fromName(string $name): static
    {
        $cases = static::getAllValues();
        foreach ($cases as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }

        throw new BusinessException(message: __('system.cacheEnumNotFound', ['name' => $name]));
    }

    /**
     * 通过值获取枚举实例
     *
     * @param string $value
     * @return static
     */
    public static function fromValue(string $value): static
    {
        $cases = static::getAllValues();
        foreach ($cases as $case) {
            if ($case->getValue() === $value) {
                return $case;
            }
        }

        throw new BusinessException(message: __('system.cacheEnumValueNotFound', ['name' => $value]));
    }

    /**
     * 创建参数异常
     *
     * @param int $expected
     * @param int $actual
     * @return BusinessException
     */
    private function createParamException(int $expected, int $actual): BusinessException
    {
        return new BusinessException(message: __('system.cacheParamsError', [
            'name' => $this->getName(),
            'needParams' => $expected,
            'issetParams' => $actual
        ]));
    }

    /**
     * 通过静态方法调用获取枚举实例
     *
     * @param string $name
     * @param array  $arguments
     * @return static
     */
    public static function __callStatic(string $name, array $arguments): static
    {
        return self::fromName($name);
    }

    /**
     * 枚举实例转换为字符串
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName();
    }

    // 获取枚举名称的抽象方法
    abstract public function getName(): string;

    // 获取枚举值的抽象方法
    abstract public function getValue(): string;

    // 获取描述的抽象方法
    abstract public function getDescription(): string;

    // 获取默认TTL的抽象方法
    abstract public function getDefaultTTL(): int;

    // 获取所有枚举值的抽象方法
    abstract protected static function getAllValues(): array;
}