<?php

namespace App\Model;

use Amber\System\Libraries\Database\DB;
use Amber\System\Model\AbstractModel;
use Amber\System\Libraries\Cache;

class UsersModel extends AbstractModel
{
    const  USER_INFO = 'user:info:';
    protected static $table = 'user';


    public function getUserInfoByUid($uid)
    {
        return $this->getUser($uid,false);
    }

    //根据uid获取用户信息，支持批量获取
    public function getUser($uid,$with_key = true)
    {
        $multi = true;
        if (!is_array($uid)) {
            $multi = false;
            $uid  = [$uid];
        }

        foreach ($uid as $id) {
            $key[] = self::USER_INFO . $id;
        }

        $callback = function ($_uid){
            return DB::table(self::$table)->where('uid', $_uid)->first();
        };

        $ret = $this->getMultipleByKeys($uid, self::USER_INFO, $callback, Cache::redis('user'),$with_key);

        if ($multi === false) {
            $ret = array_shift($ret);
        }

        $ret = $this->format(array_filter($ret));

        return $ret;
    }

    protected function format($user)
    {
        return users_avatar_domain($user);
    }
    
    public function getUserList($where,$size)
    {
        return DB::table(self::$table)->select(['uid','nickname'])->where($where)->paginate($size);
    }
}