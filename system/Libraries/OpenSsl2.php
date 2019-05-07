<?php

namespace DongPHP\System\Libraries;

class OpenSsl
{
    const OPENSSL_KEY = 'To3QFfvGJNm84KbKG1PLzA==';//echo base64_encode(openssl_random_pseudo_bytes(32));

    const OPENSSL_IV  = '3V8lFLipE3z9XESg0s6kwQ==';//echo base64_encode(openssl_random_pseudo_bytes(16));

    public static function encrypt($input, $key=self::OPENSSL_KEY)
    {
        $encrypted =  openssl_encrypt($input, 'aes-256-cbc', base64_decode($key), OPENSSL_RAW_DATA, base64_decode(self::OPENSSL_IV));

        return base64_encode($encrypted);
    }

    public static function decrypt($sStr, $sKey=self::OPENSSL_KEY)
    {
        $encrypted = base64_decode($sStr);

        $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', base64_decode($sKey), OPENSSL_RAW_DATA, base64_decode(self::OPENSSL_IV));

        return $decrypted;
    }

    public function test()
    {
        //初始化
        $res = openssl_pkey_new();
        //生成私钥
        openssl_pkey_export($res, $private_key);
        //var_dump($private_key);exit;
        $details     = openssl_pkey_get_details($res);
        //生成公钥
        $public_key  = $details["key"];
        //var_dump($public_key);exit;

        $data = 'wutiao';

        openssl_private_encrypt($data,$crypted,$private_key);
        var_dump($crypted);exit;

    }
}
