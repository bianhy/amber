<?php

namespace Amber\System\Libraries\Database\Driver;

use Amber\System\Logger;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\QueryException;

class Connection extends MySqlConnection
{
    public static $processing = false;

    public function logQuery($query, $bindings, $time = null)
    {
        if ($time > 0.1) {
            Logger::get('mysql')->alert('[S] sql:'.$query.', bindings:'.json_encode($bindings).', time:'.$time);
        }
        Logger::get('mysql')->debug('[S] sql:'.$query . ', bindings:' . json_encode($bindings) . ', time:' . $time);
    }

    protected function run($query, $bindings, \Closure $callback)
    {
        try {
            $ret =  parent::run($query, $bindings, $callback);
            if (defined('MYSQL_AUTO_CLOSE') && MYSQL_AUTO_CLOSE == true && self::$processing === false) {
                $this->disconnect();
            }
            return $ret;
        } catch (QueryException $e) {
            Logger::get('mysql')->error(json_encode(['sql'=>$e->getSql(), 'bindings'=>$e->getBindings(), 'info'=>$e->errorInfo]));
            throw new \PDOException($e->errorInfo[2], $e->errorInfo[0]);
        }
    }

    /**
     * Get the elapsed time since a given starting point.
     *
     * @param  int    $start
     * @return float
     */
    protected function getElapsedTime($start)
    {
        return (microtime(true) - $start);
    }

}