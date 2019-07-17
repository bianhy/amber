<?php

namespace Amber\System\Model;

use Amber\System\Libraries\Redis;
use Amber\System\Model;

class AbstractModel extends Model
{
    protected $from_cache = true;

    public function resetCache(\Closure $callback)
    {
        $this->from_cache = false;
        $callback();
        $this->from_cache = true;
    }

    public function storeRedis($key, \Closure $callback, $cache = null, $expire = 3600)
    {
        $info = false;
        if (!IS_CLEAR && $this->from_cache) {
            $info = json_decode($cache->get($key), true);
        }
        if (!$info) { //没有缓存直接从数据库中查找
            $info = $callback();
            if ($info) {
                $cache->set($key, json_encode($info), $expire);
            }
        }
        return $info;
    }

    public function storeMemcache($key, \Closure $callback, $cache = null, $expire = 3600)
    {
        $info = false;
        if (!IS_CLEAR && $this->from_cache) {
            $info = $cache->get($key);
        }
        if (!$info) { //没有缓存直接从数据库中查找
            $info = $callback();
            if ($info) {
                $cache->set($key, $info, 1, $expire);
            }
        }
        return $info;
    }

    public function getMultipleByKeys($keys, $key_prefix, \Closure $callback, $cache, $with_key = true)
    {
        $cache_type = 'redis';
        $ret_keys = $info = [];
        foreach ($keys as $key) {
            $ret_keys[] = $key_prefix . ':' . $key;
        }
        if (!IS_CLEAR) {
            if ($cache instanceof Redis) {
                $cache_type = 'redis';
                $info = $cache->mget($ret_keys);
                foreach ($info as &$val) {
                    $val = json_decode($val);
                }
            } else {
                $cache_type = 'memcache';
                $info = $cache->getMulti($ret_keys, Null, \Memcached::GET_PRESERVE_ORDER);
            }
            $info = array_combine($ret_keys, $info);
        }
        $ret = [];

        foreach ($keys as $key) {
            if (isset($info[$key_prefix . ':' . $key]) && !is_null($info[$key_prefix . ':' . $key])) {
                $tmp = $info[$key_prefix . ':' . $key];
            } else {
                if ($cache_type == 'redis') {
                    $tmp = self::storeRedis($key_prefix . ':' . $key, function () use ($callback, $key) {
                        return $callback($key);
                    }, $cache);
                } else {
                    $tmp = self::storeMemcache($key_prefix . ':' . $key, function () use ($callback, $key) {
                        return $callback($key);
                    }, $cache);
                }
            }

            if ($with_key) {
                $ret[$key] = $tmp;
            } else {
                $ret[] = $tmp;
            }
        }
        return $ret;
    }
}