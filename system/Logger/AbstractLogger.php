<?php

namespace Amber\System\Logger;

use Monolog\Formatter\SwooleFormatter;
use Monolog\Handler\BrowserConsoleHandler;
use Monolog\Handler\NullHandler;
use Monolog\Handler\SocketHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

Abstract class AbstractLogger
{
    protected $logger;
    /**
     * @param $method
     * @param $arguments
     */
    public function __call($method, $arguments)
    {
        try {
            call_user_func_array(array($this->logger, $method), $arguments);
        } catch (\Exception $e) {
        }
    }

    protected function getDebugHandler($level)
    {
        if ($level != Logger::DEBUG || ENVIRONMENT == 'production') {
            return new NullHandler(Logger::DEBUG);
        }

        if (IS_CLI) {
            $opt        = getopt('c:a:d',['debug']);
            if (isset($opt['d']) || isset($opt['debug'])) {
                return (new StreamHandler('php://output', Logger::DEBUG));
            }
        }

        return (!defined('IS_DEBUG') || IS_DEBUG != true) ? new NullHandler(Logger::DEBUG) : new BrowserConsoleHandler(Logger::DEBUG);
    }

    protected function getSocketHandler($level = Logger::ERROR)
    {
        $handler = new SocketHandler('tcp://' . TCP_LOG_SERVER_HOST . ':' . TCP_LOG_SERVER_PORT, $level);
        $handler->setConnectionTimeout(0.2);
        $handler->setFormatter(new SwooleFormatter());
        return $handler;
    }
}