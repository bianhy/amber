<?php
/*
 * 记录用户操作时间，如果小于限制时间，提示错误
 */

namespace Amber\System\Libraries\User;

use Amber\System\Libraries\Cache;

class UserPostTime
{
    public static function record($uid, $limit = 5, $type = '')
    {
        $last_time = Cache::redis('cache')->get(self::getKey($uid, $type));
        if (NOW_TIME - $limit < $last_time) {
            throw new \ErrorException('请不要频繁操作', 404);
        } else {
            Cache::redis('cache')->set(self::getKey($uid, $type), NOW_TIME);
        }
    }

    protected static function getKey($uid, $type)
    {
        return 'last_post:' . $uid . ':' . $type;
    }
}