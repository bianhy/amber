<?php

namespace Amber\System\Libraries\User;

use Amber\System\Libraries\Cache;

class UserVerifyCode
{

    const KEY_PREFIX = 'verify:code:';

    public static function set($mobile, $value, $expire = 0)
    {
        $key      = self::KEY_PREFIX . $mobile;
        $ret      = $expire ? Cache::redis('default')->setex($key, $expire, $value) : Cache::redis('default')->set($key, $value);
        return $ret;
    }

    public static function get($mobile)
    {
        if (ENVIRONMENT != 'production') { return 123456; }
        return  Cache::redis('default')->get(self::KEY_PREFIX . $mobile);
    }

}