<?php

namespace Amber\System;

class View
{
    protected static $path = null;

    public static function setViewPath($path)
    {
        self::$path = $path;
    }

    public static function show($filename, $data = array(), $output = true)
    {
        self::$path || self::$path = APP_PATH . '/Views/';

        if ($data) {
            if (is_array($data)) {
                extract($data, EXTR_SKIP);
            } elseif (is_object($data)) {
                $data = get_object_vars($data);
                extract($data, EXTR_SKIP);
            } else {
                trigger_error(__FUNCTION__ . ': unsupported variable type of data', E_USER_ERROR);
            }
        }

        $view_file = self::$path . '/' . $filename . '.php';

        if ($output) {
            return require($view_file);
        } else {
            ob_start();
            require $view_file;
            $view_content = ob_get_clean();

            return $view_content;
        }
    }
}
