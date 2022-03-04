<?php

namespace App;

use Doctrine\DBAL\DriverManager;

class Database
{
    private static $connection = null;

    public static function connection()
    {
        if (self::$connection === null) {

            $connectionParams = [
                'dbname' => 'friendsapp',
                'user' => 'root',
                'password' => 'Dunkans&grecis88t',
                'host' => 'localhost',
                'driver' => 'pdo_mysql',
            ];
            self::$connection = DriverManager::getConnection($connectionParams);
        }
        return self::$connection;
    }

}