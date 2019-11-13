<?php

namespace Pussle\Processors;

use \PDO;
use Sailor\Core\LoggerFactory as Logger;
use Pussle\DataType\MySQLDataType;

class DatabaseProcessor
{
    const FETCH_ASSOC = PDO::FETCH_ASSOC;

    private static $conn;
    private static $stmt;
    public static function init($config)
    {
        try {
            self::$conn = self::createConnection($config);
            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            Logger::error('Database connection failed: ' . $e->getMessage());
        }
    }

    public static function execute($sql, $params=null)
    {
        try {
            if (!empty(self::$conn)) {
                self::$stmt = self::$conn->prepare($sql);
            }
        } catch (\PDOException $e) {
            Logger::error('Prepare SQL failed: ' . $e->getMessage());
        }

        try {
            if (is_array($params) && !empty($params)) {
                foreach ($params as $name => &$param) {
                    self::$stmt->bindParam($name, $param);
                }
            }
        } catch (\PDOException $e) {
            Logger::error('Binding params failed: ' . $e->getMessage());
        }

        try {
            self::$stmt->execute();
        } catch (\PDOException $e) {
            Logger::error($e->getMessage());
            return false;
        }
        return true;
    }

    public static function lastInsertId()
    {
        return self::$conn->lastInsertId();
    }

    public static function fetch($dataType=self::FETCH_ASSOC)
    {
        try {
            $rs = self::$stmt->fetch($dataType);
        } catch (\PDOException $e) {
            Logger::error('Fetch Failed: ' . $e->getMessage());
            return false;
        }
        return !empty($rs) ? $rs : [];
    }

    public static function fetchAll($dataType=self::FETCH_ASSOC)
    {
        try {
            $rs = self::$stmt->fetchAll($dataType);
        } catch (\PDOException $e) {
            Logger::error('Fetch Failed: ' . $e->getMessage());
        }
        return !empty($rs) ? $rs : [];
    }

    public static function fetchCount()
    {
        try {
            $rs = self::$stmt->fetch(PDO::FETCH_COLUMN);
        } catch (\PDOException $e) {
            Logger::error('Fetch Failed: ' . $e->getMessage());
        }
        return $rs;
    }

    private static function createConnection($config)
    {
        $setting = [];
        $setting['{db}']       = isset($config['db']) ? $config['db'] : 'mysql';
        $setting['{host}']     = isset($config['host']) ? $config['host'] : 'localhost';
        $setting['{dbname}']   = isset($config['dbname']) ? $config['dbname'] : '';
        $setting['{charset}']  = isset($config['charset']) ? $config['charset'] : 'utf8';

        $connection = '{db}:host={host};dbname={dbname};charset={charset}';
        return new PDO(strtr($connection, $setting), $config['username'], $config['password']);
    }
}