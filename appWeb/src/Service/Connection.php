<?php

namespace App\Service;

use Doctrine\DBAL\Driver\Connection as ConnectionInterface;
use App\Service\Result as APIResult;
class Connection implements ConnectionInterface, \Doctrine\DBAL\Driver\ServerInfoAwareConnection
{
private $host;
private $port;
private $user;
private $password;

    public function __construct($params, $driver)
    {
        $this->host = $params['host']?? "";
        $this->port = $params['port']?? "";
        $this->user = $params['user']?? "";
        $this->password = $params['password']?? "";
    }

    function getNativeConnection()
    {
        return fopen($this->host.":".$this->port."/api".$this->user.$this->password, 'r');
    }
    function beginTransaction()
    {
    }
    function commit()
    {
    }
    function exec(string $sql): int
    {
    }
    function lastInsertId($name = null)
    {
    }
    function prepare(string $sql): \Doctrine\DBAL\Driver\Statement
    {
    }
    function query(string $sql): \Doctrine\DBAL\Driver\Result
    {
        return new APIResult();
    }
    function quote($value, $type =  \Doctrine\DBAL\ParameterType::STRING)
    {
    }
    function rollBack()
    {
    }

    function getServerVersion()
    {
        return "0.1";
    }
    function detectDatabasePlatform()
    {
        return "My_SQL";
    }
}
