<?php

namespace Amber\System\Libraries\Database\Driver;

use Amber\System\Logger;

class PDO extends \PDO
{

    protected $dsn      ='';
    protected $username ='';
    protected $password ='';
    protected $options  ='';
    protected $config   = [];

    protected $external_logger  = '';

    /**
     * @var \PDO
     */
    protected $pdo;

    public function __construct($dsn, $username, $password, $options, $config) {
        $this->dsn             = $dsn;
        $this->username        = $username;
        $this->password        = $password;
        $this->options         = $options;
        $this->config          = $config;
        $this->pdo             = new \PDO($dsn, $username, $password, $options);
    }

    public function prepare($sql, $option=[])
    {
        if (!empty($this->config['table_alias'])) {
            $sql = preg_replace('/([from|update|into]\s+)`?' . $this->config['table'] . '`? ?/is', '\1`' . $this->config['table_alias'] . '` ', $sql, 2);
            Logger::get('mysql')->debug('[R] sql:'.$sql);
        }
        return $this->pdo->prepare($sql, $option);
    }

    public function exec($sql)
    {
        if (!empty($this->config['table_alias'])) {
            $sql = preg_replace('/([from|update|into]\s+)`?' . $this->config['table'] . '`? ?/is', '\1`' . $this->config['table_alias'] . '` ', $sql, 2);
            Logger::get('mysql')->debug('[R] sql:'.$sql);
        }
        return $this->pdo->prepare($sql);
    }

    public function lastInsertId($seqname = NULL)
    {
        return $this->pdo->lastInsertId($seqname);
    }



    public function __call($method,$arguments) {
        return  call_user_func_array(array($this->pdo,$method),$arguments);
    }

}