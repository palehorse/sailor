<?php
namespace Pussle\ORM\DB;

use PDO;
use Sailor\Core\Config;
use Pussle\ORM\SQL\Command;
use Pussle\ORM\DB\AccessResult;

class Database
{
    /** @var PDO */
    private static $connection;

    public static function run(Command $command)
    {
        if (!self::$connection instanceof PDO) {
            self::$connection = self::createConnection();
        }

        $sql = $command->build();
        $stmt = self::$connection->prepare($sql);
        $params = [];
        if (method_exists($command, 'getParams') && !empty($command->getParams())) {
            $params = $command->getParams();
        }

        if (!is_null($where = $command->getWhere())) {
            $params = array_merge($params, $where->getParams());
        }

        $stmt->execute($params);
        return new AccessResult($stmt);
    }

    public static function execute($sql, $params = null)
    {
        if (!self::$connection instanceof PDO) {
            self::$connection = self::createConnection();
        }

        $stmt = self::$connection->prepare($sql);
        $stmt->execute($params);
        return new AccessResult($stmt);
    }

    public static function getLastInsertId()
    {
        if (self::$connection instanceof PDO) {
            return self::$connection->lastInsertId();
        }
        return false;
    }

    public static function beginTransaction()
    {
        if (self::$connection instanceof PDO) {
            return self::$connection->beginTransaction();
        }
        return false;
    }

    public static function commit()
    {
        if (self::$connection instanceof PDO) {
            return self::$connection->commit();
        }
        return false;
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