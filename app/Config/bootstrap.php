<?php
/*
|--------------------------------------------------------------------------
| set timezone and header
|--------------------------------------------------------------------------
*/
ini_set("display_errors", "on");
error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');
header('Cache-Control: private, max-age=0, no-cache');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header("Content-type:text/html;charset=utf-8");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");


/*
|--------------------------------------------------------------------------
| set define var
|----------------------------------------
.----------------------------------
*/
//时间常量
define('NOW_TIME',         time());
define('NOW_DATE_TIME',    date('Y-m-d H:i:s'));


//是否命令行
define('IS_CLI', (PHP_SAPI == 'cli') ? true : false);
//是否显示debug
define('IS_DEBUG', (isset($_REQUEST['debug']) && $_REQUEST['debug'] == 1) ? true : false);
//是否清理缓存
define('IS_CLEAR', (isset($_REQUEST['is_clear']) && $_REQUEST['is_clear'] == 1) ? true : false);
//REQUEST METHOD
define('REQUEST_METHOD', IS_CLI ? 'GET' : $_SERVER['REQUEST_METHOD']);
define('IS_GET', REQUEST_METHOD == 'GET' ? true : false);
define('IS_POST', REQUEST_METHOD == 'POST' ? true : false);
define('IS_PUT', REQUEST_METHOD == 'PUT' ? true : false);
define('IS_DELETE', REQUEST_METHOD == 'DELETE' ? true : false);
//是否自动关闭mysql连接
define('MYSQL_AUTO_CLOSE', false);
//app主目录
define('APP_PATH', dirname(dirname(__FILE__)) . '/');
//公共目录
define('PUBLIC_PATH', dirname(dirname(dirname(__FILE__))));
//框架目录
define('SYS_PATH', PUBLIC_PATH.'/system/');

//模板目录
define('VIEW_PATH', dirname(dirname(__FILE__)) . '/Views/');

/*
|--------------------------------------------------------------------------
| 预定义命令行模式下的，环境变量
|--------------------------------------------------------------------------
*/
if (IS_CLI) {
    $options = getopt('', ['environment::']);
    if (isset($options['environment'])) {
        defined('ENVIRONMENT') || define('ENVIRONMENT', $options['environment']);
    }
}

$autoloader = require_once PUBLIC_PATH . '/vendor/autoload.php';
$autoloader->addPsr4("Amber\\System\\", SYS_PATH);
$autoloader->addPsr4("App\\", APP_PATH);
