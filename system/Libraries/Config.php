<?php

namespace Amber\System\Libraries;

class Config
{
    public static $config;

    public static function loadFile($file, $public=false)
    {
        $key = $file.'|'.(string)$public;

        if (empty(self::$config[$key])) {
            $dir = $public ? dirname(dirname(__FILE__)) : APP_PATH;
            self::$config[$key] = include_once $dir."/Config/".$file.".php";
        }

        return self::$config[$key];
    }

    public static function get($key, $public=false)
    {
        if (!$key) {
            throw new \Exception('请输入要加载的配置文件');
        }
        $store_key = $key.'|'.(string)$public;
        $argvs  = explode('.', $key);
        $file   = array_shift($argvs);
        if (isset(self::$config[$store_key])) {
            return self::$config[$store_key];
        }

        $config = self::loadFile($file, $public);
        foreach ($argvs as $v) {
            if (isset($config[$v])) {
                $config = $config[$v];
            } else {
                return null;
            }
        }
        self::$config[$store_key] = $config;
        return $config;
    }

    /**
     * 根据运行环境取对应的配置
     * @param $key
     * @return null
     * @throws \Exception
     */
    public static function environment($key)
    {
        return self::get(ENVIRONMENT.'/'.$key);
    }
}
