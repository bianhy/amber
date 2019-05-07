<?php

namespace App\Model;

use Amber\System\Libraries\Database\DB;
use Amber\System\Libraries\Strings;
use Amber\System\Model\AbstractModel;

class AccountsModel extends AbstractModel
{
    protected static $table = 'accounts';

    public function newAccount($phone, $password)
    {
        $data['token']       = Strings::randString();
        $data['phone']       = $phone;
        $data['password']    = md5($data['token'] . "|" . $password);
        $data['register_ip'] = Strings::getClientIp();
        $data['create_dt']   = NOW_DATE_TIME;
        $uid                 = DB::table(self::$table)->insertGetId($data);
        return $uid;
    }

    public function getAccountByMobile($mobile)
    {
        return DB::table(self::$table)->where(['phone'=>$mobile])->first();
    }
}