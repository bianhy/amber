<?php

namespace Amber\System\Libraries\Database\Driver;

use Amber\System\Logger;
use Illuminate\Database\Connectors\MySqlConnector;


class Connector extends MySqlConnector
{

    static $instance = [];

    public function createConnection($dsn, array $config, array $options)
    {
        $username = array_get($config, 'username');
        $password = array_get($config, 'password');

        $time_start = microtime(1);
        $connection = new PDO($dsn, $username, $password, $options, $config);
        Logger::get('mysql')->debug("[T] connect info:" . "dsn:host=" . $dsn);
        Logger::get('mysql')->debug("[T] connect time:" . (microtime(1) - $time_start));
        return $connection;
    }

}