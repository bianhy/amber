<?php

namespace Amber\System;

use Amber\System\Logger\AbstractLogger;
use Monolog\Logger as MonologLogger;

class Logger extends AbstractLogger
{
    public static $container = null;

    private $name = null;

    private static $instances;

    /**
     * @param $name
     * @return MonologLogger
     */
    public static function get($name)
    {
        $key = $name;
        if (!isset(self::$instances[$key]) || !(self::$instances[$key] instanceof self)) {
            self::$instances[$key] = new self($key);
        }
        return self::$instances[$key];
    }

    public function __construct($name)
    {
        if (method_exists(__NAMESPACE__ . '\Logger', 'get' . ucfirst($name))) {
            $this->logger = call_user_func_array([__NAMESPACE__ . '\Logger', 'get' . ucfirst($name)], []);
        } else if (class_exists(__NAMESPACE__ . '\Logger\\'.ucfirst($name).'Logger')) {
            $logger_name  = __NAMESPACE__ . '\Logger\\'.ucfirst($name).'Logger';
            $this->logger = new $logger_name();
        } else {
            $this->name    = $name;
            $logger        = new MonologLogger($name);
            $logger->pushHandler($this->getDebugHandler(MonologLogger::DEBUG));
            $logger->pushHandler($this->getSocketHandler(MonologLogger::ERROR));
            $this->logger = $logger;
        }
        return $this;
    }
}
