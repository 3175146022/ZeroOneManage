<?php
declare(strict_types=1);

namespace App\Helper\Support;

use App\Http\Common\Enums\Cache;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Redis\Redis;

/**
 * Redis 缓存支持类
 */
final class RedisCacheSupport
{
    #[Inject]
    protected Redis $redis;

    /**
     * 设置缓存
     *
     * @param Cache      $key       缓存键
     * @param mixed      $value     缓存值
     * @param string|int ...$params 缓存键参数
     * @return bool
     */
    public function set(Cache $key, mixed $value, string|int ...$params): bool
    {
        $redisKey = $key->getKey(...$params);
        $ttl = $key->getDefaultTTL();

        if (is_array($value) || is_object($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        //no expire
        if($ttl === -1) return $this->redis->set($redisKey, $value);

        return $this->redis->setex($redisKey, $ttl, $value);
    }

    /**
     * 获取缓存
     *
     * @param Cache      $key       缓存键
     * @param string|int ...$params 缓存键参数
     * @return mixed
     */
    public function get(Cache $key, string|int ...$params): mixed
    {
        $redisKey = $key->getKey(...$params);
        $value = $this->redis->get($redisKey);

        if ($value === false) {
            return null;
        }

        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        return $value;
    }

    /**
     * 删除缓存
     *
     * @param Cache      $key       缓存键
     * @param string|int ...$params 缓存键参数
     * @return bool
     */
    public function del(Cache $key, string|int ...$params): bool
    {
        $redisKey = $key->getKey(...$params);
        return (bool)$this->redis->del($redisKey);
    }

    /**
     * 检查缓存是否存在
     *
     * @param Cache      $key       缓存键
     * @param string|int ...$params 缓存键参数
     * @return bool
     */
    public function has(Cache $key, string|int ...$params): bool
    {
        $redisKey = $key->getKey(...$params);
        return (bool)$this->redis->exists($redisKey);
    }

    /**
     * decr 原子减
     *
     * @param Cache $key
     * @param string|int ...$params
     * @return bool
     */
    public function decr(Cache $key,string|int ...$params) : mixed
    {
        $redisKey = $key->getKey(...$params);
        // return $redisKey;
        return (bool)$this->redis->decr($redisKey);
    }

    /**
     * decrBy
     *
     * @param Cache $key
     * @param int $increment
     * @param string|int ...$params
     * @return bool
     */
    public function decrBy(Cache $key, int $increment,string|int ...$params) : bool
    {
        $redisKey = $key->getKey(...$params);
        return (bool)$this->redis->decrBy($redisKey,$increment);
    }

    /**
     * 原子增
     *
     * @param Cache $key
     * @param int $increment
     * @param string|int ...$params
     * @return bool
     */
    public function incr(Cache $key, int $increment, string|int ...$params): bool
    {
        $redisKey = $key->getKey(...$params);
        return (bool)$this->redis->incr($redisKey, $increment);
    }

    /**
     * 原子增
     *
     * @param Cache $key
     * @param int $increment
     * @param string|int ...$params
     * @return bool
     */
    public function incrby(Cache $key, int $increment,string|int ...$params): bool
    {
        $redisKey = $key->getKey(...$params);
        return (bool)$this->redis->incrBy($redisKey, $increment);
    }

    /**
     * 为键设置过期时间（秒）
     *
     * @param Cache $key
     * @param $ttl
     * @param string|int ...$params
     * @return bool
     */
    public function expire(Cache $key, $ttl,string|int ...$params): bool
    {
        $redisKey = $key->getKey(...$params);
        return $this->redis->expire($redisKey, $ttl);
    }

    /**
     *
     * 给定time_out 内获取redis锁
     *
     * @param Cache $key
     * @param int $lock_time
     * @param int $time_out
     * @param string|int ...$params
     * @return bool
     */
    public function getLock(Cache $key, int $lock_time = 3, int $time_out = 10, string|int ...$params): bool
    {
        $cacheKey = $key->getKey(...$params);
        $time = time() + $time_out;

        while (time() < $time) {
            $is_lock = $this->redis->set($cacheKey, '1',['NX', 'EX' => $lock_time]);
            if ($is_lock) {
                return true;
            }

            usleep(1000 * 100);  //100毫秒请求一次
        }

        return false;
    }

    /**
     * 释放锁
     *
     * @param Cache $key
     * @param string|int ...$params
     * @return bool
     */
    public function releaseLock(Cache $key, string|int ...$params): bool
    {
        $cacheKey = $key->getKey(...$params);

        return (bool) $this->redis->del($cacheKey);
    }

    /**
     * 插入有序集合数据
     *
     * @param Cache $key
     * @param int $score
     * @param mixed $value
     * @return bool
     */
    public function zAdd(Cache $key, int $score, mixed $value, string|int ...$params): bool
    {
        $cacheKey = $key->getKey(...$params);

        return (bool)$this->redis->zAdd($cacheKey, $score, $value);
    }

    /**
     * hset
     *
     * @param Cache $key
     * @param $name
     * @param mixed $value
     * @param string|int ...$params
     * @return bool
     */
    public function hashSet(Cache $key, $name, mixed $value, string|int ...$params): bool
    {
        $cacheKey = $key->getKey(...$params);
        if (is_array($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        }
        return FALSE !== $this->redis->hSet($cacheKey, $name, $value);
    }

    /**
     * hGet
     *
     * @param Cache $key
     * @param $name
     * @param string|int ...$params
     * @return mixed
     */
    public function hashGet(Cache $key, $name, string|int ...$params): mixed
    {
        $cacheKey = $key->getKey(...$params);
        return $this->redis->hGet($cacheKey, $name);
    }

    /**
     * hGetAll
     *
     * @param Cache $key
     * @param string|int ...$params
     * @return array
     */
    public function hashGetAll(Cache $key, string|int ...$params): array
    {
        $cacheKey = $key->getKey(...$params);
        return (array)$this->redis->hGetAll($cacheKey);
    }

}