<?php
namespace Amber\System\Libraries;

class Input
{

    public static function postString($param, $default = null)
    {
        return self::string($param, $default , 'post');
    }

    public static function postInt($param, $default = 0)
    {
        return self::int($param, $default , 'post');
    }

    public static function string($param, $default = null, $type = 'request')
    {
        $value = self::getValue($type);
        $tmp   = isset($value[$param]) ? trim($value[$param]) : (is_array($default) ? $default[0] : $default);
        if ( is_array($default) && !in_array($tmp, $default) ) {
            return $default[0];
        }
        return $tmp;
    }

    public static function int($param, $default = 0, $type = 'request')
    {
        $value = self::getValue($type);
        $tmp   = isset($value[$param]) ? intval($value[$param]) : (is_array($default) ? $default[0] : $default);
        if ( is_array($default) && !in_array($tmp, $default) ) {
            return $default[0];
        }
        return $tmp;
    }

    private static function getValue($type)
    {
        parse_str(file_get_contents("php://input"),$out);
        foreach ($out as $key => $value) {
            $_POST[$key] = $value;
        }

        switch (strtoupper($type)) {
            case 'POST':
                $value = $_POST;
                break;
            case 'GET':
                $value = $_GET;
                break;
            case 'COOKIE':
                $value = $_COOKIE;
                break;
            case 'SESSION':
                $value = $_SESSION;
                break;
            default:
                $value = $_REQUEST;
                break;
        }
        return $value;
    }
} 