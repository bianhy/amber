<?php

namespace App\Model;

use Amber\System\Libraries\Cache;
use Amber\System\Libraries\Database\DB;
use Amber\System\Model\AbstractModel;

class StartModel extends AbstractModel
{
    const     START_BANNER  = 'start:banner:';
    protected static $table = 'start_banner';

    public function getBanner($device_type = 'ios')
    {
        $key  = self::START_BANNER.$device_type;
        $device_type = $device_type == 'ios' ? 1 : 2;
        $callback = function () use($device_type){
            return  DB::table(self::$table)
                ->where(['device_type'=>$device_type,'status'=>1])
                ->where('start_time','<',NOW_DATE_TIME)
                ->where('end_time','>',NOW_DATE_TIME)
                ->first();
        };
        return $this->storeRedis($key,$callback,Cache::redis('default'));
    }
}