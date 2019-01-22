<?php
namespace Amber\System\Logger;

use Monolog\Logger;

class MemcacheLogger extends AbstractLogger
{
    public function __construct()
    {
        $logger = new Logger(__CLASS__);
        $logger->pushHandler($this->getDebugHandler(Logger::DEBUG));
        $logger->pushHandler($this->getSocketHandler(Logger::ERROR));
        $this->logger = $logger;
        return $this->logger;
    }
}