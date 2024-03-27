<?php

namespace App\Service;

use Doctrine\DBAL\Driver\Connection as ConnectionInterface;

class Connection implements ConnectionInterface
{
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
    }
    function quote($value, $type =  \Doctrine\DBAL\ParameterType::STRING)
    {
    }
    function rollBack()
    {
    }
}
