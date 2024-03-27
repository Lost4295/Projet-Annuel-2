<?php

namespace App\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement as StatementInterface;

class Statement implements StatementInterface
{
    function bindParam($param, &$variable, $type =  \Doctrine\DBAL\ParameterType::STRING, $length = null)
    {
    }
    function bindValue($param, $value, $type =  \Doctrine\DBAL\ParameterType::STRING)
    {
    }
    function execute($params = null): \Doctrine\DBAL\Driver\Result
    {
    }
}
