<?php
/**
    全球 IPv4 地址归属地数据库(17MON.CN 版)
    高春辉(pAUL gAO) <gaochunhui@gmail.com>
    Build 20141117 版权所有 17MON.CN
    (C) 2006 - 2014 保留所有权利
    请注意及时更新 IP 数据库版本
    数据问题请加 QQ 群: 346280296
    Code for PHP 5.3+ only
*/

namespace Amber\System\Libraries\Ipku;

class IP
{
    private static $ip     = NULL;

    private static $fp     = NULL;
    private static $offset = NULL;
    private static $index  = NULL;

    private static $cached = array();

    public static function find($ip)
    {
        if (empty($ip) === TRUE)
        {
            return 'N/A';
        }

        $nip   = gethostbyname($ip);
        $ipdot = explode('.', $nip);

        if ($ipdot[0] < 0 || $ipdot[0] > 255 || count($ipdot) !== 4)
        {
            return 'N/A';
        }

        if (isset(self::$cached[$nip]) === TRUE)
        {
            return self::$cached[$nip];
        }

        if (self::$fp === NULL)
        {
            self::init();
        }

        $nip2 = pack('N', ip2long($nip));

        $tmp_offset = ((int)$ipdot[0] * 256 + (int)$ipdot[1]) * 4;
        $start      = unpack('Vlen', self::$index[$tmp_offset] . self::$index[$tmp_offset + 1] . self::$index[$tmp_offset + 2] . self::$index[$tmp_offset + 3]);

        $index_offset = $index_length = NULL;
        $max_comp_len = self::$offset['len'] - 262144 - 4;
        for ($start = $start['len'] * 9 + 262144; $start < $max_comp_len; $start += 9)
        {
            if (self::$index{$start} . self::$index{$start + 1} . self::$index{$start + 2} . self::$index{$start + 3} >= $nip2)
            {
                $index_offset = unpack('Vlen', self::$index{$start + 4} . self::$index{$start + 5} . self::$index{$start + 6} . "\x0");
                $index_length = unpack('nlen', self::$index{$start + 7} . self::$index{$start + 8});

                break;
            }
        }

        if ($index_offset === NULL)
        {
            return 'N/A';
        }

        fseek(self::$fp, self::$offset['len'] + $index_offset['len'] - 262144);

        self::$cached[$nip] = explode("\t", fread(self::$fp, $index_length['len']));

        return self::$cached[$nip];
    }

    public static function getInfo($ip)
    {
        $ipInfo = array(
            'ip'           => $ip,
            'countryName'  => null,
            'regionName'   => null,
            'cityName'     => null
        );
        $result = self::find($ip);
        if($result != 'N/A'){
            $ipInfo['countryName'] = trim($result[0]);
            $ipInfo['regionName']  = trim($result[1]);
            $ipInfo['cityName']    = trim($result[2]);
            $ipInfo['cityCode']    = trim($result[9]? $result[9]:'00000');
        }
        return $ipInfo;
    }

    private static function init()
    {
        if (self::$fp === NULL)
        {
            self::$ip = new self();

            self::$fp = fopen(__DIR__ . '/mydata4vipday1.datx', 'rb');
            if (self::$fp === FALSE)
            {
                throw new Exception('Invalid mydata4vipday1.datx file!');
            }

            self::$offset = unpack('Nlen', fread(self::$fp, 4));
            if (self::$offset['len'] < 4)
            {
                throw new Exception('Invalid mydata4vipday1.datx file!');
            }

            self::$index = fread(self::$fp, self::$offset['len'] - 4);
        }
    }

    public function __destruct()
    {
        if (self::$fp !== NULL)
        {
            fclose(self::$fp);
        }
    }
}

?>