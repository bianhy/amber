<?php

namespace Amber\System\Libraries;

abstract class AbstractDayCounter
{
    protected static $data;
    const KEY_PREFIX = 'day:counter:';

    protected static function getNextDayUnixTime()
    {
        return strtotime(date("Y-m-d 23:59:59"))+1;
    }

    public static function set($key, $type, $param='', $value=1)
    {
        $seconds = self::getNextDayUnixTime() - time();
        $key     = self::getKey($key, $type, $param);
        self::getRedis()->setex($key, $seconds, $value);
        self::$data[$key] = $value;
        return self::$data[$key];
    }

    public static function add($key, $type, $param='', $value=1)
    {
        $key      = self::getKey($key, $type, $param);
        $total    = self::getRedis()->incrbyfloat($key, $value);
        if ($total == $value) { //第一次调用ADD时，设置key的有效期
            self::getRedis()->expireAt($key, self::getNextDayUnixTime());
        }
        self::$data[$key] = $total;
        return self::$data[$key];
    }

    public static function get($key, $type, $param='')
    {
        $key = self::getKey($key, $type, $param);
        if (!isset(self::$data[$key])) {
            self::$data[$key] = (int)self::getRedis()->get($key);
        }
        return self::$data[$key];
    }

    protected static function getRedis()
    {
        return Cache::redis('default');
    }

    public static function clear($key, $type, $param='')
    {
        $key = self::getKey($key, $type, $param);
        if (isset(self::$data[$key])) {
            unset(self::$data[$key]);
        }
        return self::getRedis()->del([$key]);
    }

    protected static function getKey($key, $type, $param='')
    {
        if (!$param) {
            $key = date('Ymd').'_'.$key.'_'.$type;
        } elseif (is_array($param)) {
            $key = date('Ymd').'_'.$key.'_'.$type.'_'.json_encode($param);
        } else {
            $key = date('Ymd').'_'.$key.'_'.$type.'_'.$param;
        }
        return static::KEY_PREFIX.md5($key);
    }
}