<?php

namespace Amber\System\Libraries\Database\Driver;

use Amber\System\Logger;
use Illuminate\Database\Query\Builder;

class Processor extends \Illuminate\Database\Query\Processors\Processor
{
    public function processInsertGetId(Builder $query, $sql, $values, $sequence = null)
    {
        Connection::$processing = true;
        $query->getConnection()->insert($sql, $values);

        $id = $query->getConnection()->getPdo()->lastInsertId($sequence);
        Connection::$processing = false;
        if (defined('MYSQL_AUTO_CLOSE') && MYSQL_AUTO_CLOSE == true ) {
            $query->getConnection()->disconnect();
        }
        return is_numeric($id) ? (int) $id : $id;
    }

}