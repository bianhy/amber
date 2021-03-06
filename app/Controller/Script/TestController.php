<?php

namespace App\Controller\Script;

use App\Controller\AbstractController;
use Amber\System\Libraries\Database\DB;
use Amber\System\Libraries\Strings;

define('ONLY_CLI', true);//脚本文件定义只能在命令行模式下执行

class TestController extends AbstractController
{
    /**
     * 测试脚本,linux
     * /opt/app/php-5.6.20/bin/php /opt/wwwroot/amber/app/www/index.php -c='App\Controller\Script\TestController' -a=repair
     * windows:
     * php index.php -c=App\Controller\Script\TestController -a=repair
     */
    public function repair()
    {
        $start = microtime(1);
        $list = DB::table('user')->get();
        if (!$list){
            $this->outError('无可修复的用户信息');
        }
        foreach ($list as $value){
            $old_name = $value['nickname'];
            $new_name = Strings::randString(4,4);
            $ret = DB::table('user')->where(['uid'=>$value['uid']])->limit(1)->update(['nickname'=>$new_name]);
            if ($ret){
                echo 'uid:'.$value['uid'].' :old_name:'.$old_name.' 变更为：'.$new_name.PHP_EOL;
            }else{
                echo 'uid:'.$value['uid'].'执行失败'.PHP_EOL;
            }
        }
        echo 'All Done . 用时：'.(microtime(1) - $start) . '秒';
    }
}
