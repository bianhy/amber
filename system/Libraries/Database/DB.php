<?php

namespace Amber\System\Libraries\Database;

use Amber\System\Libraries\Database\Driver\Processor;
use Amber\System\Libraries\DataConfigLoader;
use Amber\System\Libraries\Input;
use Illuminate\Container\Container;
use Illuminate\Database\Connection;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Pagination\Paginator;

class DB
{
    protected static $instance;
    protected $connections;

    /**
     * @var ConnectionFactory
     */
    protected $connectionFactory = null;

    public function __construct()
    {
        $container  = new Container();
        $container->bind('db.connector.mysql_hash',  'Amber\System\Libraries\Database\Driver\Connector');
        $container->bind('db.connection.mysql_hash', 'Amber\System\Libraries\Database\Driver\Connection');
        $this->connectionFactory = new ConnectionFactory($container);

        //设置分页信息
        self::setPaginator();
    }

    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static;
        }
        return self::$instance;
    }

    protected static function setPaginator() {
        Paginator::currentPageResolver(function()
        {
            return Input::int('page', 1);
        });
    }

    /**
     * @param $table
     * @param null $hash
     * @return \Illuminate\Database\Query\Builder
     * @throws \Exception
     */
    public static function table($table, $hash = null)
    {
        if (!$table) {
            throw new \Exception('builder table 不能为空');
        }

        $parse = DataConfigLoader::parseTable($table);
        return self::connect($table, $hash)->table($parse['table']);
    }

    /**
     * @param $table
     * @param null $hash
     * @return \Illuminate\Database\Connection
     */
    public static function connect($table, $hash = null)
    {
        return self::getInstance()->getConnect($table, $hash);
    }

    /**
     * @param $table
     * @param null $hash
     * @return \Illuminate\Database\Connection
     */
    protected function getConnect($table, $hash = null)
    {
        $config              = DataConfigLoader::db($table, $hash);
        $config['driver']    = 'mysql_hash';
        $config['password']  = $config['pass'];
        $config['username']  = $config['user'];
        $config['charset']   = 'utf8mb4';
        $config['collation'] = 'utf8mb4_unicode_ci';

        $key = $config['host'].':'.$config['port'].':'.$config['database'].':'.$config['table_alias'];
        if (isset($this->connections[$key]) &&  $this->connections[$key] instanceof Connection) {
            return $this->connections[$key];
        }

        $connection = $this->connectionFactory->make($config);
        $connection->setReconnector(function (Connection $connection) use ($config) {
            $fresh = $this->connectionFactory->make($config);
            $connection->setPdo($fresh->getPdo())->setReadPdo($fresh->getReadPdo());
            unset($fresh);
            return $connection;
        });
        $connection->setPostProcessor(new Processor());
        $this->connections[$key] = $connection;
        return $this->connections[$key];
    }
}
