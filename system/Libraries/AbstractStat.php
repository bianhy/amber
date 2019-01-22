<?php
/**
 * 用户统计数据
 */

namespace Amber\System\Libraries;


abstract class AbstractStat
{
    protected static $data;

    const REDIS_PREFIX = 'stat:hash:';

    protected static function getKey($key)
    {
        return static::REDIS_PREFIX.$key;
    }

    /**
     * @return \Redis
     */
    protected static function redis()
    {
        return Cache::redis('default');
    }

    public static function set($key, $status, $value)
    {
        if (is_array($status) || is_array($value)) {
            if (count($status) != count($value)) {
                throw new \ErrorException('参数错误', 786);
            }

            return static::redis()->hMset(self::getKey($key), array_combine($status,  $value));
        } else {
            return static::redis()->hSet(self::getKey($key), $status, $value);
        }
    }

    public static function add($key, $status, $value=1)
    {
        return static::redis()->hIncrByFloat(self::getKey($key), $status, $value);
    }

    public static function get($key, $status)
    {
        if (!is_array($status) &&  strpos($status, ",") !== false) {
            $status = explode(",", $status);
        }

        if (is_array($status)) {
            return static::redis()->hMGet(self::getKey($key), $status);
        } else {
            return static::redis()->hGet(self::getKey($key), $status);
        }
    }

    public static function clear($key, $status)
    {
        return static::redis()->hDel(self::getKey($key), $status);
    }
}
