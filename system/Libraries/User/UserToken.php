<?php

namespace Amber\System\Libraries\User;

use Amber\System\Libraries\Cache;

class UserToken
{
    const KEY_PREFIX = 'user:token:';
    public static function set($uid, $data)
    {
        $key      = self::KEY_PREFIX . $uid;
        $ret      = Cache::redis('user')->hMset($key, $data);
        return $ret;
    }

    public static function get($uid, $fields = 'token')
    {
        $key = self::KEY_PREFIX . $uid;
        return Cache::redis('user')->hGet($key, $fields);
    }

    public static function delete($uid, $fields = 'token')
    {
        $key = self::KEY_PREFIX . $uid;
        return Cache::redis('user')->hDel($key, $fields);
    }
}