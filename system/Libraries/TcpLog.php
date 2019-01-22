<?php

namespace Amber\System\Libraries;

class TcpLog
{
    public static $log        = array();
    public static $globalInfo = null;
    public static $dir        = false;
    public static $host       = TCP_LOG_SERVER_HOST;
    public static $port       = TCP_LOG_SERVER_PORT;

    public static function setHost($host, $port)
    {
        self::$host = $host;
        self::$port = $port;
    }

    public static function record($file, $info)
    {
        self::$log[] = array('file' => $file, 'info' => $info, 'time' => time());
    }

    public static function setGlobalInfo($info)
    {
        self::$globalInfo = $info;
    }

    public static function save($file = '', $info = '', $dir = '')
    {
        $args = func_get_args();
        if (!in_array(func_num_args(), array(1, 3))) {
            throw new \Exception('params error');
        }
        if (func_num_args() == 1) {
            self::$dir = $args[0];
        } else {
            self::record($args[0], $args[1]);
            self::$dir = $args[2];
        }

        if (count(self::$log) < 1) {
            return true;
        }

        $data = array();
        foreach (self::$log as $row) {
            if (self::$globalInfo) {
                if (is_array($row['info'])) {
                    $row['info'] = json_encode($row['info']).'|'.self::$globalInfo;
                } else {
                    $row['info'].='|'.self::$globalInfo;
                }
            }
            $data[] = array('filename' => self::$dir . '/' . $row['file'], 'log' => $row['info'], 'time' => time());
        }
        $msg = json_encode($data);

/*        $socket_client = stream_socket_client('tcp://' . self::$host . ':' . self::$port, $errno, $errstr, 1);
        stream_set_timeout($socket_client, 0, 100000);
        if (!$socket_client) {
            trigger_error("$errstr ($errno)", E_USER_NOTICE);
            return true;
        }
        fwrite($socket_client, $msg . "\r\n");
        fclose($socket_client);*/
        //清空已经发送的日志
        self::$log = array();

        return true;
    }
}
