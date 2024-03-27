<?php

namespace App\Service;

use Doctrine\DBAL\Connection;
use App\Service\Connection as APIConnection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Driver as DriverInterface;

class Driver implements DriverInterface
{
    public function createConnection(array $params): Connection
    {
        return DriverManager::getConnection($params);
    }

    function connect(array $params){
        $user = $params['user']?? "";
        $password = $params['password']?? "";
        $host = $params['host']?? "";
        $port = $params['port']?? "";
        return new APIConnection($params, new Driver());

    }
    function getDatabasePlatform(){
        return new \Doctrine\DBAL\Platforms\MySQLPlatform();
    }
    function getExceptionConverter(): DriverInterface\API\ExceptionConverter{
        dd("no");
    }
    function getSchemaManager(Connection $conn, \Doctrine\DBAL\Platforms\AbstractPlatform $platform){
        return;
    }
}
