<?php
/*
 * openssl加密解密
 * Created by PhpStorm.
 * Date: 2018/2/12
 * Time: 16:25
 */

namespace Amber\System\Libraries;

class Openssl
{
    const OPENSSL_METHOD   = 'AES-128-CBC';
    const OPENSSL_PASSWORD = '@#$%TTYU&^%FJ';

    public static function encrypt($input)
    {
        return openssl_encrypt($input, self::OPENSSL_METHOD, self::OPENSSL_PASSWORD, 0, 'soaxyjr5p0cYAep8');
    }

    public static function decrypt($output)
    {
        $code =  openssl_decrypt($output, self::OPENSSL_METHOD, self::OPENSSL_PASSWORD, 0, 'soaxyjr5p0cYAep8');
        return $code;
    }
}
