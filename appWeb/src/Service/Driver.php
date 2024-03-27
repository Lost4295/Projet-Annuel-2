<?php

namespace App\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Driver as DriverInterface;

class Driver implements DriverInterface
{
    public function createConnection(array $params): Connection
    {
        return DriverManager::getConnection($params);
    }

    function connect(array $params){
        return;
    }
    function getDatabasePlatform(){
        return;
    }
    function getExceptionConverter(): DriverInterface\API\ExceptionConverter{
        dd("no");
    }
    function getSchemaManager(Connection $conn, \Doctrine\DBAL\Platforms\AbstractPlatform $platform){
        return;
    }
}
