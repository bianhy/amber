<?php

namespace Amber\System;

class Helper
{
    public static function load($name, $public=false)
    {
        $file_path = $public === false ? APP_PATH.'Helper/'.$name.'.php'  : dirname(__FILE__).'/Helper/'.$name.'.php';
        if (file_exists($file_path)) {
            require_once $file_path;
        }
    }
}
