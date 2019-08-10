<?php

namespace Pussle\DatabaseAdapters;

use Sailor\Core\Config;
use \PDO;

class MySQLAdapter
{
    public static function run($sql)
    {
        $conn = self::createConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    private static function createConnection()
    {
        $setting = [];
        $setting['{db}']       = 'mysql';
        $setting['{host}']     = Config::get('database.DB_HOST');
        $setting['{dbname}']   = Config::get('database.DB_NAME');
        $setting['{charset}']  = Config::get('database.DB_CHARSET');

        $connection = '{db}:host={host};dbname={dbname};charset={charset}';
        return new PDO(strtr($connection, $setting), Config::get('database.DB_USER'), Config::get('database.DB_PWD'));
    }
}