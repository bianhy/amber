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

    public function getMultipleByKeys($keys, $key_prefix, \Closure $callback, $cache, $with_key=true)
    {
        $ret_keys = [];
        foreach ($keys as $key) {
            $ret_keys[] = $key_prefix.':' . $key;
        }
        if (!IS_CLEAR) {
            if ($cache instanceof Redis) {
                $info = $cache->mget($ret_keys);
                foreach ($info as &$val) {
                    $val = json_decode($val);
                }
            } else {
                $info = $cache->getMulti($ret_keys, Null ,\Memcached::GET_PRESERVE_ORDER);
            }
        }
        $ret = [];

        foreach ($keys as $key) {
            if(isset($info[$key_prefix .':'. $key]) && !is_null($info[$key_prefix .':'. $key])) {
                $tmp = $info[$key_prefix .':'. $key];
            } else {
                $tmp = $callback($key);
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