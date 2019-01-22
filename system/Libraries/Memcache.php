<?php

namespace Amber\System\Libraries;

use Amber\System\Logger;


if (!class_exists('MemcacheException')) {
    class MemcacheException extends \Exception{}
}

class Memcache
{
    /**
     * @var \Memcache
     */
    public $memcache;

    public $host;

    public $port;

    /**
     * @var \Monolog\Logger
     */
    public $logger;

    public $connect = false;

    private static $instances;

    public function __construct($host, $port)
    {
        $this->memcache = new \Memcache();
        $this->host     = $host;
        $this->port     = $port;
        $this->logger   = Logger::get('memcache');
        if (!$this->memcache->connect($this->host, $this->port)) {
            throw new MemcacheException('addserver error:'.$this->host.':'.$this->port);
        }
    }

    public static function getInstance($host, $port)
    {
        $key   = $host.':'.$port;
        if (!isset(self::$instances[$key]) || !(self::$instances[$key] instanceof self)) {
            self::$instances[$key] = new self($host, $port);
        }
        return self::$instances[$key];
    }

    public function __call($method, $arguments)
    {
        $this->logger->debug('command:'.$method.','.json_encode($arguments));
        $start_time = microtime(true);
        $ret = call_user_func_array(array($this->memcache, $method), $arguments);

        $use_time = (microtime(true)- $start_time);
        $this->logger->debug('use time:'.$use_time);
        if ($ret === false && strtolower($method) != 'get') {
            $this->logger->error('method:'.$method.', arguments:'.json_encode($arguments));
            throw new MemcacheException('memcache error => method:'.$method.', arguments:'.json_encode($arguments), 404);
        }

        if ($use_time > 0.1) {
            $this->logger->alert('use time: '.$use_time.', method:'.$method.', arguments:'.json_encode($arguments));
        }

        return $ret;
    }
}
